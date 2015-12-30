<?php
namespace Seo\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use Seo\Model\Behavior\SeoBehavior;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\Entity;
use Cake\Event\Event;
use Cake\Utility\Inflector;
use Cake\Routing\Router;
use Cake\Routing\RouteCollection;
use Cake\ORM\TableRegistry;

use Cake\I18n\I18n;



class ArticlesTable extends Table
{
    
}


/**
 * Seo\Model\Behavior\SeoBehavior Test Case
 */
class SeoBehaviorTest extends TestCase
{

    use \FriendsOfCake\TestUtilities\AccessibilityHelperTrait;

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

        TableRegistry::clear();

        Router::reload();
        Router::scope('/', function($routes) {
            $routes->fallbacks('DashedRoute');
        });

        $this->Articles = TableRegistry::get('Articles', [
            'className' => 'Seo\Test\TestCase\Model\Behavior\ArticlesTable'
        ]);
        $this->Articles->addBehavior('Seo.Seo');

        $this->SeoBehavior = new SeoBehavior($this->Articles);

        $this->setReflectionClassInstance($this->SeoBehavior);
        //$this->defaultReflectionTarget = $this->SeoBehavior; // (optional)

        $this->defaultEntity = $this->Articles->find()->first();

        $this->locale = I18n::locale();

        $this->defaultConfig = [
            'urls' => [
                [
                    'url' => [
                        'prefix' => false,
                        'controller' => 'articles',
                        'action' => 'view',
                        '_' => [
                            'slug' => 'slug'
                        ]
                    ],
                    'canonical' => true,
                    'title' => '{{title}} - test',
                    'meta_tags' => [
                        'description' => [
                            'content' => '{{content}}'
                        ],
                        'og:description' => [
                            'content' => '{{content}}',
                            'is_property' => true
                        ],
                        'og:locale' => [
                            'callback' => 'getLocale',
                            'is_property' => true
                        ],
                        'robots' => [
                            'content' => 'NOINDEX, NOFOLLOW'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SeoBehavior);
        unset($this->Articles);
        unset($this->router);

        parent::tearDown();

        TableRegistry::clear();
    }

    /**
     * Get the entity's uri based on the configuration options.
     */
    public function testGetUri()
    {
        $parameters = [
            $this->defaultEntity,
            $this->defaultConfig['urls'][0]
        ];

        $expected = '/articles/view?slug=test-title-one';
        $actual = "";
        $actual = $this->callProtectedMethod('_getUri', $parameters, $this->SeoBehavior);
        $this->assertEquals($expected, $actual);

        $parameters = [
            $this->defaultEntity,
            [
                'url' => [
                    'prefix' => false,
                    'controller' => 'articles',
                    'action' => 'view',
                    '_callback' => [$this, 'getUriUserCallback']
                ],
            ]
        ];

        $expected = '/view/passed';
        $actual = $this->callProtectedMethod('_getUri', $parameters, $this->SeoBehavior);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Set Title based on configuration array
     */
    public function testSetSeoTitle()
    {
        $expected = 'Test title one - test';
        $result = $this->Articles->setSeoTitle($this->defaultEntity, '/view?slug=test-title-one', $this->defaultConfig['urls'][0]);
        $this->assertEquals($expected, $result);
    }

    /**
     * Set Canonical based on configuration array
     */
    public function testSetCanonical()
    {
        $expected = [
            'canonical' => Router::fullBaseUrl() . '/articles/view?slug=test-title-one',
            'active' => true
        ];
        $actual = $this->Articles->setCanonical($this->defaultEntity, '/articles/view?slug=test-title-one', $this->defaultConfig['urls'][0]);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Set Meta Tags based on configuration array
     */
    public function testSetMetaTags()
    {
        $expected = [
            [
                'name' => 'description',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni esse odit fugiat, officiis tempore, numquam excepturi, vitae pariatur maxime, alias est quaerat consequatur vel cum exercitationem sint ex ab hic.',
                'is_http_equiv' => false,
                'is_property' => false
            ],
            [
                'name' => 'og:description',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni esse odit fugiat, officiis tempore, numquam excepturi, vitae pariatur maxime, alias est quaerat consequatur vel cum exercitationem sint ex ab hic.',
                'is_http_equiv' => false,
                'is_property' => true
            ],
            [
                'name' => 'og:locale',
                'content' => $this->locale,
                'is_http_equiv' => false,
                'is_property' => true
            ],
            [
                'name' => 'robots',
                'content' => 'NOINDEX, NOFOLLOW',
                'is_http_equiv' => false,
                'is_property' => false
            ],
        ];
        $actual = $this->Articles->setMetaTags($this->defaultEntity, '/articles/view?slug=test-title-one', $this->defaultConfig['urls'][0]);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Full logic to save an entity
     * @todo
     */
    public function testSaveDefaultUri()
    {
        
    }

    /**
     * Format Template
     */
    public function testFormatTemplate()
    {
        $expected = 'Test title one - test';
        $actual = $this->callProtectedMethod('_formatTemplate', [$this->defaultConfig['urls'][0]['title'], $this->defaultEntity], $this->SeoBehavior);
        $this->assertEquals($expected, $actual);
    }

    /**
     * User callback passed throw configuration url[_callback] key.
     */
    public function getUriUserCallback($entity, $urlParams)
    {
        return '/view/passed';
    }
}
