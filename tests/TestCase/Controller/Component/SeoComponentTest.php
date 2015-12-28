<?php
namespace Seo\Test\TestCase\Controller\Component;

use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Seo\Controller\Component\SeoComponent;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

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

        $this->Controller = new SeoComponentTestController(new Request('/articles/view/test-title-one'));
        $this->Controller->startupProcess();

        // $registry = new ComponentRegistry($this->controller);
        // $this->seoComponent = new SeoComponent($registry);
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

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
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
        $expected = '<link rel="canonical" href="http://test.local/articles/view/test-title-one"/>';
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
            '<meta property="og:title" content="Open graph Seo Title"/>'
        ];
        
        $result = $this->Controller->Seo->getMetaTags($uriEntity->seo_meta_tags);
        $this->assertEquals($expected, $result);
    }
}
