<?php

namespace Seo\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * This task class …
 */
class GenerateTask extends SeoTask
{

	/**
     * Holds tables found on connection.
     *
     * @var array
     */
    protected $_tables = [];

    /**
     * Holds the model names
     *
     * @var array
     */
    protected $_modelNames = [];

    /**
     * Tables to skip
     *
     * @var array
     */
    public $skipTables = ['i18n', 'cake_sessions'];

    public $errors = [];

    public $ignored = [];

    /**
     * Patterns to exclude some generic tables
     *
     * @var array
     */
    public $skipTablePatterns = ['/phinxlog$/'];

	public function main($name = null)
	{
		parent::main();
	   	$name = $this->_getName($name);

        if (empty($name)) {
            $this->out('Choose a model to create SEO datas from the following:');
            foreach ($this->listUnskipped() as $table) {
                $this->out('- ' . $this->_camelize($table));
            }
            return true;
        }

        $this->generate($this->_camelize($name));
	}

    /**
     * Generate SEO entries for the given model name.
     * The process will try to add SEO entries for each entities. 
     * If an uri is already in the db the process is by-passed.
     * In case of errors ou ignored uris, you will be informed
     * in the command output
     * 
     * @param string $name Name of the model
     */
	public function generate($name) {
        if (!in_array($name, $this->listUnskipped())) {
            $this->error(sprintf('The table %s does not exist', $name));
        }
		$table = $this->getTable($name);
        $model = $this->getTableObject($name, $table);
        $behaviors = $model->behaviors()->loaded();
        if (!$model->behaviors()->has('Seo')) {
        	$this->error(sprintf('The table %s does not have the Seo behavior', $table));
        }

        $entries = $model->find()->all();
        
        foreach ($entries as $entry) {

        	$seoUri = $model->saveDefaultUri($entry);
        	if (!$seoUri) {
        		$this->ignored[] = $entry->{$model->displayField()};
        		continue;
        	}

        	if ($seoUri->errors()) {
        		foreach ($seoUri->errors() as $tableError => $fieldErrors) {
        			$this->errors[$entry->{$model->displayField()}][] = [
        				'value' => $seoUri->{$tableError}->{key($fieldErrors)},
        				'log' => $fieldErrors
        			];
        		}
        	}
        }

        if (!empty($this->ignored)) {
        	$this->err(sprintf('The process has ignored %d uris. See the list below:', count($this->ignored)), 2);
        	foreach ($this->ignored as $key => $ignored) {
        		$this->err($ignored);
        	}
        }

        if (!empty($this->errors)) {
        	$this->err(sprintf('The process has failed %d time(s)', count($this->errors)), 2);
        	foreach ($this->errors as $key => $error) {
        		$this->err($key . ':');
        		$this->err(var_export($error), 2);

        	}
        }
	}

	/**
     * Get a model object for a class name.
     *
     * @param string $className Name of class you want model to be.
     * @param string $table Table name
     * @return \Cake\ORM\Table Table instance
     */
    public function getTableObject($className, $table)
    {
        if (TableRegistry::exists($className)) {
            return TableRegistry::get($className);
        }
        return TableRegistry::get($className, [
            'name' => $className,
            'table' => $table,
            'connection' => ConnectionManager::get($this->connection)
        ]);
    }

	/**
     * Get the table name for the model being baked.
     *
     * Uses the `table` option if it is set.
     *
     * @param string $name Table name
     * @return string
     */
    public function getTable($name)
    {
        if (isset($this->params['table'])) {
            return $this->params['table'];
        }
        return Inflector::underscore($name);
    }

	protected function _getAllTables()
    {
        $db = ConnectionManager::get($this->connection);
        if (!method_exists($db, 'schemaCollection')) {
            $this->err(
                'Connections need to implement schemaCollection() to be used with Seo.Generate.'
            );
            return $this->_stop();
        }
        $schema = $db->schemaCollection();
        $tables = $schema->listTables();
        if (empty($tables)) {
            $this->err('Your database does not have any tables.');
            return $this->_stop();
        }
        sort($tables);
        return $tables;
    }

    /**
     * Outputs the a list of possible models or controllers from database
     *
     * @return array
     */
    public function listAll()
    {
        if (!empty($this->_tables)) {
            return $this->_tables;
        }

        $this->_modelNames = [];
        $this->_tables = $this->_getAllTables();
        foreach ($this->_tables as $table) {
        	foreach ($this->skipTablePatterns as $pattern) {
        		if (preg_match($pattern, $table)) {
        			array_push($this->skipTables, $table);
        		}
        	}
        	
            $this->_modelNames[] = $this->_camelize($table);
        }
        return $this->_tables;
    }

    /**
     * Outputs the a list of unskipped models or controllers from database
     *
     * @return array
     */
    public function listUnskipped()
    {
        $this->listAll();
        return array_diff($this->_tables, $this->skipTables);
    }

    /**
     * Gets the option parser instance and configures it.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
	{
		$parser = parent::getOptionParser();

		$parser->addArgument('name', [
            'help' => 'Name of the model to create SEO datas.'
        ])->addArgument('table', [
			'help' => 'The table name to use if you have non-conventional table names.'
		]);

		return $parser;
	}
}