<?php
namespace Seo\Test\TestCase\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Seo\Controller\Component\SeoComponent;


class SeoComponentTestController extends Controller
{
    public $components = ['Seo.Seo'];

}

/**
 * Seo\Controller\Component\SeoComponent Test Case
 */
class SeoComponentTest extends TestCase
{

    public $Controller = null;

    public $seoComponent = null;

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

        $request = new request();
        $response = new response();

        $this->Controller = new SeoComponentTestController(new Request('/articles/view?slug=test-title-one'));
        $this->Controller->viewBuilder()->className('View');
        $this->Controller->viewBuilder()->layout('default');
        $this->Controller->startupProcess();

        $this->View = $this->Controller->createView();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Controller);

        parent::tearDown();
    }

    public function testSeoToHtml()
    {
        $event = new Event('Event', $this->View);
        $actual = $this->Controller->Seo->seoToHtml($event);
        $titleBlock = 'Seo Test title one';
        $metaBlock = '<link rel="canonical" href="http://test.local/articles/view?slug=test-title-one"/><meta name="description" content="Seo description content"/><meta name="robots" content="index, follow"/><meta property="og:title" content="Open graph Seo Title"/><meta http-equiv="Content-Language" content="fr_FR"/>';

        $this->assertEquals($titleBlock, $this->View->Blocks->get('title'));
        $this->assertEquals($metaBlock, $this->View->Blocks->get('meta'));

        // Bad uri
        $this->Controller->request->here = '/foo';   
        $event = new Event('Event', $this->Controller);
        $actual = $this->Controller->Seo->seoToHtml($event);
        $this->assertNull($actual);
    }
    
    public function testGetUriDatas()
    {
        // Default, based on this->request->here
        $result = $this->Controller->Seo->getUriDatas();
        $this->assertInstanceOf('Seo\Model\Entity\SeoUri', $result);

        // Uri as parameter
        $result = $this->Controller->Seo->getUriDatas('/articles/view/test-title-three');
        $this->assertInstanceOf('Seo\Model\Entity\SeoUri', $result);

        // Not approved uri.
        $result = $this->Controller->Seo->getUriDatas('/articles/view/test-title-two');
        $this->assertNull($result);
    }

    public function testGetCanonicalTag()
    {
        $uriEntity = $this->Controller->Seo->getUriDatas();
        $expected = '<link rel="canonical" href="http://test.local/articles/view?slug=test-title-one"/>';
        $result = $this->Controller->Seo->getCanonicalTag($uriEntity);
        $this->assertEquals($expected, $result);
    }

    public function testGetTitle()
    {
        $uriEntity = $this->Controller->Seo->getUriDatas();
        
        $expected = 'Seo Test title one';
        $result = $this->Controller->Seo->getTitle($uriEntity);
        $this->assertEquals($expected, $result);

        $expected = 'Seo Test title one - suffix';
        $this->Controller->Seo->config('defaults.suffix', ' - suffix');
        $result = $this->Controller->Seo->getTitle($uriEntity);
        $this->assertEquals($expected, $result);

        $expected = 'prefix - Seo Test title one';
        $this->Controller->Seo->config('defaults.prefix', 'prefix - ');
        $this->Controller->Seo->config('defaults.suffix', null);
        $result = $this->Controller->Seo->getTitle($uriEntity);
        $this->assertEquals($expected, $result);

        $expected = 'prefix - Seo Test title one - suffix';
        $this->Controller->Seo->config('defaults.prefix', 'prefix - ');
        $this->Controller->Seo->config('defaults.suffix', ' - suffix');
        $result = $this->Controller->Seo->getTitle($uriEntity);
        $this->assertEquals($expected, $result);
    }

    public function testGetMetaTags()
    {
        $uriEntity = $this->Controller->Seo->getUriDatas();

        $expected = [
            '<meta name="description" content="Seo description content"/>',
            '<meta name="robots" content="index, follow"/>',
            '<meta property="og:title" content="Open graph Seo Title"/>',
            '<meta http-equiv="Content-Language" content="fr_FR"/>'
        ];
        
        $result = $this->Controller->Seo->getMetaTags($uriEntity->seo_meta_tags);
        $this->assertEquals($expected, $result);
    }
}
