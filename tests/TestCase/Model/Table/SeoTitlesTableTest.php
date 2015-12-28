<?php
namespace Seo\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Seo\Model\Table\SeoTitlesTable;

/**
 * Seo\Model\Table\SeoTitlesTable Test Case
 */
class SeoTitlesTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.seo.seo_titles',
        'plugin.seo.seo_uris',
        'plugin.seo.seo_canonicals',
        'plugin.seo.seo_meta_tags'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('SeoTitles') ? [] : ['className' => 'Seo\Model\Table\SeoTitlesTable'];
        $this->SeoTitles = TableRegistry::get('SeoTitles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SeoTitles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
