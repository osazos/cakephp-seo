<?php
namespace Seo\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use Seo\Model\Table\SeoUrisTable;

/**
 * Seo\Model\Table\SeoUrisTable Test Case
 */
class SeoUrisTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.seo.seo_uris',
        'plugin.seo.seo_canonicals',
        'plugin.seo.seo_meta_tags',
        'plugin.seo.seo_titles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('SeoUris') ? [] : ['className' => 'Seo\Model\Table\SeoUrisTable'];
        $this->SeoUris = TableRegistry::get('SeoUris', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SeoUris);

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

    public function testFindApproved()
    {
        $this->markTestIncomplete('Not implemented yet.');
        $query = $this->SeoUris->find('approved');
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->hydrate(false)->toArray();

        $expected = $this->SeoUris->find()->contain([
                'SeoTitles',
                'SeoCanonicals',
                'SeoMetaTags'
            ])
            ->where([
               'is_approved' => true
               ]);

        $this->assertEquals($expected, $result);
    }

    public function testGetByUri()
    {
        $approved = false;
        $uri = "/articles/view/test-title-two";

        $expected = $this->SeoUris->findByUri($uri)
            ->contain([
                'SeoTitles',
                'SeoCanonicals',
                'SeoMetaTags'
            ])->first();

        $actual = $this->SeoUris->getByUri($uri, $approved);
        $this->assertEquals($expected, $actual);
    }

    public function testGetByUriApproved()
    {
        $approved = true;
        $uri = "/articles/view/test-title-one";

        $expected = $this->SeoUris->findByUri($uri)
            ->contain([
                'SeoTitles',
                'SeoCanonicals',
                'SeoMetaTags'
            ])
            ->where([
               'is_approved' => true
            ])->first();

        $actual = $this->SeoUris->getByUri($uri, $approved);
        $this->assertEquals($expected, $actual);
    }
}
