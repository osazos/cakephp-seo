<?php
namespace Seo\Shell;

use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Core\Configure;


/**
 * Seo shell command.
 * 
 * Provide Task
 */
class SeoShell extends Shell
{

    public $tasks = [
        'Seo.Generate'
    ];

    public function startup()
    {
        Configure::write('debug', true);
        Cache::disable();

    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('generate', [
            'help' => 'Generate SEO entries based on a model configuration.',
            'parser' => $this->Generate->getOptionParser()
        ]);
        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main() 
    {
        $this->out('Main Seo Shell');
    }
}
