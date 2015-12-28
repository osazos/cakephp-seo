<?php
namespace Seo\Model\Behavior;

use Cake\I18n\I18n;

trait SeoTrait
{
    
    /**
     * Shortcut to retrieve the actual locale set.
     * @return string The locale name
     */
    public function getLocale()
    {
        return I18n::locale();
    }
}
