<?php
namespace Seo\View\Cell;

use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Cell;

/**
 * Seobox cell
 */
class SeoboxCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @param string $uri An uri like /articles/view/foo-bar
     * @return void
     * @todo See how to add default Seo configuration for the model after an uri has been created.
     */
    public function display($uri)
    {
        $this->loadModel('Seo.SeoUris');
        $seoUri = $this->SeoUris->getByUri($uri);

        if (!$seoUri) {
            // Create a new uri.
            $seoUriEntity = $this->SeoUris->newEntity();
            $seoUriEntity->uri = $uri;
            $seoUriEntity->is_approved = true;

            if ($this->SeoUris->save($seoUriEntity)) {
                $seoUri = $this->SeoUris->getByUri($uri);
            }
        }

        $this->set(compact('seoUri'));
    }
}
