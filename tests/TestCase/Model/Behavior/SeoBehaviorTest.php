<?php
namespace Seo\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\TestSuite\TestCase;
use Cake\Cache\Cache;
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

    public $defaultConfig = [
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
                            'callback' => [
                                'function' => 'getLocale'

                            ],
                            'is_property' => true
                        ],
                        'robots' => [
                            'content' => 'NOINDEX, NOFOLLOW'
                        ]
                    ]
                ]
            ]
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

        Cache::disable();

        Router::reload();
        //Router::fullBaseUrl('http://test.local');
        Router::scope('/', function($routes) {
            $routes->fallbacks('DashedRoute');
        });

        $this->Articles = TableRegistry::get('Articles', [
            'className' => 'Seo\Test\TestCase\Model\Behavior\ArticlesTable'
        ]);
        $this->Articles->addBehavior('Seo.Seo', $this->defaultConfig);

        $this->SeoBehavior = new SeoBehavior($this->Articles, $this->defaultConfig);

        $this->setReflectionClassInstance($this->SeoBehavior);
        //$this->defaultReflectionTarget = $this->SeoBehavior; // (optional)

        $this->defaultEntity = $this->Articles->find()->first();

        $this->locale = I18n::locale();
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

    public function testBeforeDelete()
    {
        $entity = $this->Articles->get(1);
        $this->SeoBehavior->beforeDelete(New Event('beforeDelete', []), $entity, new ArrayObject);
        $this->SeoUris = TableRegistry::get('Seo.SeoUris');
        $expected = $this->SeoUris->getByUri('/articles/view?slug=test-title-one');
        $result = $this->getProtectedProperty('_seoUriEntities', $this->SeoBehavior);
        $this->assertEquals($expected, $result[0]);
    }

    public function testAfterDelete()
    {
        $this->SeoUris = TableRegistry::get('Seo.SeoUris');

        $entity = $this->Articles->get(1);
        
        //$this->SeoBehavior->beforeDelete(New Event('beforeDelete', []), $entity, new ArrayObject);
        //$result = $this->getProtectedProperty('_seoUriEntities', $this->SeoBehavior);
        //$this->assertEquals($expected, $result[0]);
        $this->Articles->delete($entity);
        $this->assertNull($this->SeoUris->getByUri('/articles/view?slug=test-title-one'));
    }

    /**
     * Full logic to save an entity
     * @todo
     */
    public function testAfterSave()
    {
        $article = TableRegistry::get('Articles')->newEntity([
            'title' => 'A new fucking good article',
            'slug' => 'a-new-fucking-good-article',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni esse odit fugiat, officiis tempore, numquam excepturi, vitae pariatur maxime, alias est quaerat consequatur vel cum exercitationem sint ex ab hic.',
            'created' => '2015-01-01 18:00:00',
            'modified' => '2015-01-01 18:00:00'
        ]);

        $this->SeoUris = TableRegistry::get('Seo.SeoUris');
        $this->Articles->save($article);
        $actual = $this->SeoUris->getByUri('/articles/view?slug=a-new-fucking-good-article');
        
        $this->assertEquals('A new fucking good article - test', $actual->seo_title->title);
        $this->assertEquals(Router::fullBaseUrl() . '/articles/view?slug=a-new-fucking-good-article', $actual->seo_canonical->canonical);
        $metas = $this->Articles->behaviors()->get('Seo')->config('urls.0.meta_tags');
        $this->assertEquals(count($metas), count($actual->seo_meta_tags));
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

        $actual = $this->Articles->setSeoTitle($this->defaultEntity, '/view?slug=test-title-one', []);
        $this->assertFalse($actual);
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

        $actual = $this->Articles->setCanonical($this->defaultEntity, '/articles/view?slug=test-title-one', []);
        $this->assertFalse($actual);
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

        $actual = $this->Articles->setMetaTags($this->defaultEntity, '/articles/view?slug=test-title-one', []);
        $this->assertFalse($actual);
    }

    /**
     * Set Meta Tags witch callback for content
     */
    public function testSetMetaTagsWitchCallback()
    {
        $config = [
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
                            'callback' => [
                                'function' => 'getDescription',
                                'options' => ['fields' => 'content']
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $entity = $this->Articles->newEntity([
            'id' => '1',
            'title' => 'Test title one',
            'slug' => 'test-title-one',
            'content' => '<p>Lorem ipsum dolor sit amet</p> <ul><li>consectetur adipisicing elit.</li><li>Magni esse odit fugiat</li><li>officiis tempore</li></ul><p>numquam excepturi, vitae pariatur maxime, alias est quaerat consequatur vel cum exercitationem sint ex ab hic.&nbsp;</p>',
            'created' => '2012-12-12 12:12:12',
            'modified' => '2013-01-01 11:11:11',
        ]);
        $actual = $this->Articles->setMetaTags($entity, '/articles/view?slug=test-title-one', $config['urls'][0]);
        
        $expected = [
            [
                'name' => 'description',
                'content' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Magni esse odit fugiat officiis tempore numquam excepturi, vitae pariatur maxime, alias est quaerat',
                'is_http_equiv' => false,
                'is_property' => false
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Format Template
     */
    public function testFormatTemplate()
    {
        $expected = 'Test title one - test';
        $actual = $this->callProtectedMethod('_formatTemplate', [$this->defaultConfig['urls'][0]['title'], $this->defaultEntity], $this->SeoBehavior);
        $this->assertEquals($expected, $actual);


        $actual = $this->callProtectedMethod('_formatTemplate', ['{{xxxx}}', $this->defaultEntity], $this->SeoBehavior);
        $this->assertEquals('', $actual);
    }

    /**
     * User callback passed throw configuration url[_callback] key.
     */
    public function getUriUserCallback($entity, $urlParams)
    {
        return '/view/passed';
    }
}
