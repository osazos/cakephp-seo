<?php
namespace Seo\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Core\ConventionsTrait;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Generic methods used by Seo Tasks.
 *
 */
class SeoTask extends Shell {

	use ConventionsTrait;

	/**
     * The db connection being used for baking
     *
     * @var string
     */
    public $connection = null;

    /**
     * Initialize hook.
     *
     * Populates the connection property, which is useful for tasks of tasks.
     *
     * @return void
     */
    public function initialize()
    {
        if (empty($this->connection) && !empty($this->params['connection'])) {
            $this->connection = $this->params['connection'];
        }
    }

    /**
     *
     * @return void
     */
    public function main()
    {
        if (isset($this->params['connection'])) {
            $this->connection = $this->params['connection'];
        }
    }

	/**
     * Get the option parser for this task.
     *
     * This base class method sets up some commonly used options.
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addOption('connection', [
            'short' => 'c',
            'default' => 'default',
            'help' => 'The datasource connection to get data from.'
		]);

		return $parser;
    }

    /**
     * Handles splitting up the plugin prefix and classname.
     *
     * Sets the plugin parameter and plugin property.
     *
     * @param string $name The name to possibly split.
     * @return string The name without the plugin prefix.
     */
    protected function _getName($name)
    {
        if (strpos($name, '.')) {
            list($plugin, $name) = pluginSplit($name);
            $this->plugin = $this->params['plugin'] = $plugin;
        }
        return $name;
    }
}