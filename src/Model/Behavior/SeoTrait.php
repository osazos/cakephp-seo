<?php
namespace Seo\Model\Behavior;

use Cake\I18n\I18n;
use Cake\ORM\Entity;
use Cake\Utility\Text;

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

    /**
     * 
     */
    public function getDescription($data, $entity, $options)
    {
        $content = '';
        
        extract($options);
        if (isset($fields)) {
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    if ($entity->has($field)) {
                        $temp = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $entity->$field);
                        $temp = preg_replace('/&[a-z]+;/', '', $temp);
                        $temp = preg_replace('/<\/[a-z]+><[a-z]+>/', ' ', $temp);
                        $temp = preg_replace('/[\s]{1,}/', ' ', $temp);
                        $content .= strip_tags($temp);
                    }
                }
            } else {
                if ($entity->has($fields)) {
                    $temp = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $entity->$fields);
                    $temp = preg_replace('/&[a-z]+;/', '', $temp);
                    $temp = preg_replace('/<\/[a-z]+><[a-z]+>/', ' ', $temp);
                    $temp = preg_replace('/[\s]{1,}/', ' ', $temp);
                    $content .= strip_tags($temp);
                }
            }
        }
        return Text::truncate($content, 160, ['ellipsis' => '', 'exact' => false, 'html' => false]);
    }
}
