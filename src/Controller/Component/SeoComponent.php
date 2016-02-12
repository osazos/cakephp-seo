<?php
namespace Seo\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\View\StringTemplateTrait;

/**
 * Seo component
 */
class SeoComponent extends Component implements EventListenerInterface
{

    use StringTemplateTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'excludePrefix' => ['admin'],
        'defaults' => [
            'title' => 'Default Title',
            'prefix' => null,
            'suffix' => null
        ],
        'templates' => [
            'meta' => '<meta{{attrs}}/>',
            'canonical' => '<link rel="canonical" href="{{content}}"/>'
        ]
    ];

    /**
     * Controller instance
     *
     * @var \Cake\Controller\Controller
     */
    protected $_controller;

    public $SeoUris;

    /**
     * Initialize Hook
     * @param array $config Configuration array
     * @return void
     */
    public function initialize(array $config)
    {
        $this->_controller = $this->_registry->getController();
        $this->SeoUris = TableRegistry::get('Seo.SeoUris');
    }

    /**
     * Implemented Events
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'View.beforeLayout' => 'seoToHtml'
        ];
    }

    /**
     * Set To Html
     *
     * @param Cake\Event\Event $event Event
     * @return mixed void or null if no uri founded
     */
    public function seoToHtml(Event $event)
    {
        if (array_key_exists('prefix', $this->_controller->request->params) 
            && in_array($this->_controller->request->params['prefix'], $this->config('excludePrefix'))) {
            return;
        }
        $uri = $this->getUriDatas();

        if (!$uri) {
            return null;
        }

        $event->subject()->assign('title', $this->getTitle($uri));

        if ($uri->seo_meta_tags) {
            $metas = $this->getMetaTags($uri->seo_meta_tags);
            $event->subject()->append('meta', implode('', $metas));
        }

        $event->subject()->prepend('meta', $this->getCanonicalTag($uri));
    }

    /**
     * Get Canonical Tag
     *
     * @param Cake\ORM\Entity $uri A complete uri entity
     * @return string The formated Html Canonical tag
     */
    public function getCanonicalTag(Entity $uri)
    {
        $canonical = null;
        if ($uri->has('seo_canonical') && $uri->seo_canonical->active) {
            $canonical = $this->formatTemplate('canonical', ['content' => $uri->seo_canonical->canonical]);
        }
        return $canonical;
    }

    /**
     * Get Title
     *
     * @param Cake\ORM\Entity $uri A complete uri entity
     * @return string
     */
    public function getTitle(Entity $uri)
    {
        $title = $this->config('defaults.title');
        $prefix = $this->config('defaults.prefix');
        $suffix = $this->config('defaults.suffix');

        if ($uri->has('seo_title') && $uri->seo_title) {
            $title = $uri->seo_title->title;
        }

        return $prefix . $title . $suffix;
    }

    /**
     * Get Meta Tags
     *
     * @param array $metaTags Array of seo_meta_tags entities
     * @return array An array of html formated meta tags
     */
    public function getMetaTags(array $metaTags)
    {
        $metas = [];

        foreach ($metaTags as $key => $meta) {

            if (!$meta->content) {
                continue;
            }

            $options = [];
            $attr = 'name';

            if ($meta->is_http_equiv) {
                $attr = 'http-equiv';
            } elseif ($meta->is_property) {
                $attr = 'property';
            }

            $options = [
                $attr => $meta->name,
                'content' => $meta->content
            ];

            $metas[] = $this->formatTemplate('meta', [
                'attrs' => $this->templater()->formatAttributes($options, ['block', 'type'])
            ]);
        }
        return $metas;
    }

    /**
     * Get Uri Datas
     *
     * @param string $uri The Uri we want to get infos
     * @return Cake\ORM\Entity
     */
    public function getUriDatas($uri = null)
    {
        if ($uri === null) {
            $uri = $this->request->here;
        }

        $uriDatas = Cache::remember('uri_' . md5($uri), function () use ($uri) {
            $SeoUris = TableRegistry::get('Seo.SeoUris');
            return $SeoUris->findByUri($uri)->find('approved')->first();
        }, 'seo');

        return $uriDatas;
    }
}
