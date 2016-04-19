<?php
namespace Seo\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Seo behavior
 */
class SeoBehavior extends Behavior
{

    use \Seo\Model\Behavior\SeoTrait;

    /**
     * Default configuration.
     *
     * @var array
     * @todo Add the choice to merge or overwrite configuration.
     */
    protected $_defaultConfig = [
        'urls' => [
            [
                'url' => [
                    'prefix' => false,
                    'action' => 'view',
                    '_' => [
                        'slug' => 'slug'
                    ]
                ],
                'locale' => null,
                'title' => 'Seo default title',
                'canonical' => true,
                'meta_tags' => [
                    // 'og:type' => [
                    //     'content' => 'website',
                    //     'is_property' => true
                    // ],
                    // 'og:description' => [
                    //     'content' => '{{content}}',
                    //     'is_property' => true
                    // ],
                    // 'og:locale' => [
                    //     'callback' => 'getLocale',
                    //     'is_property' => true
                    // ],
                    // 'twitter:description' => [
                    //     'content' => '{{content}}',
                    //     'is_property' => true
                    // ],
                ]
            ]
        ]
    ];

    /**
     * Entities Seo.SeoUris stored in the beforeDelete Event
     * Needed because sometimes we have a url callback defined to find
     * some routes.
     * So we have to do some logic before the entity (for which the behavior
     * is attached) is deleted
     *
     * @var array
     */
    protected $_seoUriEntities = [];

    /**
     * Instance of Seo.SeoUrisTable
     *
     * @var Seo\Model\Table\SeoUrisTable
     */
    protected $_SeoUris = null;

    /**
     * Initialize method
     *
     * @param array $config Configuration options
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_SeoUris = TableRegistry::get('Seo.SeoUris');

        // Add Relations.
        $this->_table->hasMany('SeoUris', [
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'SeoUris.model' => $this->_table->alias(),
                //'SeoUris.locale' => I18n::locale()
            ],
            'dependent' => true
        ]);
    }

    /**
     * After Save Callback
     *
     * @param Cake/Event/Event $event The afterSave event that was fired.
     * @param Cake/ORM/Entity $entity The entity
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterSave(Event $event, Entity $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            $this->saveDefaultUri($entity);
        } else {
            // Change route.
            if ($entity->dirty()) {
                $SeoUris = TableRegistry::get('Seo.SeoUris');

                $urlsConfig = $this->config('urls');
                
                foreach ($urlsConfig as $key => $url) {
                    $uri = $this->_getUri($entity, $url);
                    $seoUri = $SeoUris->find()->contain(['SeoCanonicals'])->where([
                        'model' => $this->_table->alias(),
                        'foreign_key' => $entity->id,
                        'locale' => isset($url['locale']) ? $url['locale'] : null
                    ])->first();
                    
                    // $seoUriCanonical = Router::fullBaseUrl() . $uri;
                    // $seoUri->seo_canonical->set('canonical', $seoUriCanonical);

                    if ($seoUri) {
                        $SeoUris->patchEntity($seoUri, ['uri' => $uri]);
                        $SeoUris->save($seoUri);
                    }
                }
            }
        }

        \Cake\Cache\Cache::clear(false, 'seo');
    }

    /**
     * Before Delete Callback
     *
     * @param Cake/Event/Event $event The beforeDelete event that was fired.
     * @param Cake/ORM/Entity $entity The entity
     * @param ArrayObject $options Options
     * @return void
     */
    public function beforeDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        // $this->beforeDeleteUri($entity);
        return;
    }

    /**
     * After Delete Callback
     *
     * @param Cake/Event/Event $event The afterDelete event that was fired.
     * @param Cake/ORM/Entity $entity The entity
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterDelete(Event $event, Entity $entity, ArrayObject $options)
    {
        // $seoUri = "Seo\\Model\\Entity\\SeoUri";
        // foreach ($this->_seoUriEntities as $seoUriEntity) {
        //     if ($seoUriEntity instanceof $seoUri) {
        //         $this->_SeoUris->delete($seoUriEntity);
        //     }
        // }
        \Cake\Cache\Cache::clear(false, 'seo');
        //return;
    }

    /**
     * Delete Uri
     *
     * Delete Seo Entries for an Entity
     * @param Cake\ORM\Entity $entity The entity
     * @return void
     */
    public function beforeDeleteUri(Entity $entity)
    {
        foreach ($this->config('urls') as $key => $url) {
            $uri = $this->_getUri($entity, $url);
            $this->_seoUriEntities[] = $this->_SeoUris->getByUri($uri);
        }
    }

    /**
     * Save Default Uri
     *
     * Save defaults uri, title, meta tags defined
     * for the model based on the configuration array.
     *
     * @param Cake\ORM\Entity $entity The Entity
     * @param mixed $urlsConfig array of urls configuration to generate tags. If false,
     *        it will use the behavior configuration $this->config('urls') RECOMMENDED.
     * @return mixed Entity SeoUri or False if the uri already exists.
     */
    public function saveDefaultUri(Entity $entity, $urlsConfig = false, $checkApproved = true)
    {
        if (!$urlsConfig) {
            $urlsConfig = $this->config('urls');
        }

        $uris = [];

        foreach ($urlsConfig as $key => $url) {
            $uri = $this->_getUri($entity, $url);
            
            $SeoUris = TableRegistry::get('Seo.SeoUris');

            if ($checkApproved === true && $SeoUris->findByUri($uri)->find('approved')->first()) {
                return false;
            }
            
            $uriEntity = [
                'uri' => $uri,
                'model' => $this->_table->alias(),
                'foreign_key' => $entity->id,
                'locale' => isset($url['locale']) ? $url['locale'] : null,
                'is_approved' => true,
                'seo_title' => [
                    'title' => $this->setSeoTitle($entity, $uri, $url)
                ],
                'seo_canonical' => $this->setCanonical($entity, $uri, $url),
                'seo_meta_tags' => $this->setMetaTags($entity, $uri, $url)
            ];

            $seoUriEntity = $SeoUris->newEntity($uriEntity);
            $SeoUris->save($seoUriEntity);
            $uris[] = $seoUriEntity;
        }
        return $uris;
    }

    /**
     * @param Cake\ORM\Entity $entity The entity
     * @param string $uri The Uri
     * @param array $config Options configuration
     * @return mixed array|false
     */
    public function setMetaTags(Entity $entity, $uri, array $config = [])
    {
        if (array_key_exists('meta_tags', $config) && is_array($config['meta_tags'])) {
            $metaTags = [];
            foreach ($config['meta_tags'] as $key => $value) {

                if (isset($value['callback'])) {
                    $defaults = [
                        'function' => '',
                        'options' => []
                    ];

                    if (!is_array($value['callback'])) {
                        throw new \Exception("callback key must be an array");
                    }

                    $callback = array_merge($defaults, $value['callback']);
                    unset($value['callback']);

                    if (is_array($callback['function'])) {
                        $value['content'] = call_user_func($callback['function'], $value, $entity, $callback['options']);
                    } else {
                        $value['content'] = call_user_func([$this, $callback['function']], $value, $entity, $callback['options']);
                    }
                }

                $metaTags[] = [
                    'name' => $key,
                    'content' => $this->_formatTemplate($value['content'], $entity),
                    'is_http_equiv' => (array_key_exists('is_http_equiv', $value)) ? $value['is_http_equiv'] : false,
                    'is_property' => (array_key_exists('is_property', $value)) ? $value['is_property'] : false
                ];
            }
            return $metaTags;
        }

        return false;
    }

    /**
     * @param Cake\ORM\Entity $entity The entity
     * @param string $uri The Uri
     * @param array $config Configuration array for the uri
     * @return mixed array|false
     */
    public function setCanonical(Entity $entity, $uri, array $config = [])
    {
        if (array_key_exists('canonical', $config) && $config['canonical'] === true) {
            return [
                'canonical' => Router::fullBaseUrl() . $uri,
                'active' => true
            ];
        }
        return false;
    }

    /**
     * @param Cake\ORM\Entity $entity The entity
     * @param string $uri The Uri
     * @param array $config Configuration array for the uri
     * @return mixed string|false ex.: My super title
     */
    public function setSeoTitle(Entity $entity, $uri, array $config)
    {
        if (array_key_exists('title', $config)) {
            return $this->_formatTemplate($config['title'], $entity);
        }
        return false;
    }

    /**
     * @param Cake\ORM\Entity $entity The entity
     * @param array $urlParams The optionals parameters to generate a route
     * @return string ex.: /articles/view/foo
     * @todo Allow named routes
     */
    protected function _getUri(Entity $entity, Array $urlParams)
    {
        $uri = null;
        if (is_array($urlParams['url'])) {
            if (array_key_exists('_callback', $urlParams['url'])) {
                $urlParams['url'] = call_user_func($urlParams['url']['_callback'], $entity, $urlParams);
            } elseif (array_key_exists('_', $urlParams['url'])) {
                foreach ($urlParams['url']['_'] as $extraParam => $value) {
                    $urlParams['url'][$extraParam] = $entity->{$value};
                }
                unset($urlParams['url']['_']);
            }
            $uri = Router::url($urlParams['url']);
        } else {
            // named route
        }

        return $uri;
    }

    /**
     * Format Template
     *
     * Return a formated template to use as content for a tag
     * ex :
     * Hello {{name}} -> Hello John
     * {{name}} is the property entity
     *
     * @param string $pattern a pattern to match
     * @param Cake\ORM\Entity $entity The entity
     * @return string
     * @todo Allow {{model.field}} syntax
     */
    protected function _formatTemplate($pattern, $entity)
    {
        $template = preg_replace_callback('/{{([\w\.]+)}}/', function ($matches) use ($entity) {
            if (preg_match('/^([_\w]+)\.(\w+)$/', $matches[1], $x)) {
                $table = TableRegistry::get(Inflector::tableize($x[1]));
                $matchEntity = $table->get($entity->{$x[1] . '_id'});
                return $matchEntity->{$x[2]};
            }
            if ($entity->has($matches[1])) {
                return $entity->{$matches[1]};
            }

        }, $pattern);

        return trim($template);
    }
}
