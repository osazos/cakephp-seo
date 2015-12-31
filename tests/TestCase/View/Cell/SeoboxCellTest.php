<?php
namespace Seo\Test\TestCase\View\Cell;

use Cake\Cache\Cache;
use Cake\TestSuite\TestCase;
use Seo\View\Cell\SeoboxCell;
use Cake\ORM\TableRegistry;

/**
 * Seo\View\Cell\SeoboxCell Test Case
 */
class SeoboxCellTest extends TestCase
{

    public $fixtures = [
        'plugin.Seo.Articles',
        'plugin.Seo.SeoUris',
        'plugin.Seo.SeoCanonicals',
        'plugin.Seo.SeoTitles',
        'plugin.Seo.SeoMetaTags',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        Cache::disable();
        $this->request = $this->getMock('Cake\Network\Request');
        $this->response = $this->getMock('Cake\Network\Response');

        $this->Seobox = new SeoboxCell($this->request, $this->response);

        $this->Seobox->viewBuilder()->className('View');
        $this->Seobox->viewBuilder()->layout('default');

        $this->View = $this->Seobox->createView();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Seobox);

        parent::tearDown();
    }

    /**
     * Test display method
     *
     * @return void
     */
    public function testDisplay()
    {
        $SeoUris = TableRegistry::get('Seo.SeoUris');
        
        $this->Seobox->display('/articles/view?slug=test-title-one');

        $expected = $SeoUris->getByUri('/articles/view?slug=test-title-one');
        $actual = $this->Seobox->viewVars['seoUri'];
        $this->assertEquals($expected, $actual);

        // Auto creates
        $this->Seobox->display('/articles/view?slug=not-saved-for-the-moment'); 
        $expected = $SeoUris->getByUri('/articles/view?slug=not-saved-for-the-moment');       
        $actual = $this->Seobox->viewVars['seoUri'];
        $this->assertEquals($expected, $actual);
    }

}
