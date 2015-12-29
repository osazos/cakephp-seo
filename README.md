# CakePHP SEO Plugin

[![License](https://poser.pugx.org/orgasmicnightmare/cakephp-seo/license)](https://packagist.org/packages/orgasmicnightmare/cakephp-seo)

A SEO plugin for cakePHP 3 to manage seo tags with ease. It comes with a Behavior, a Component, a View Cell and the default admin crud actions for each tables. 

- The behavior helps you to generate some default tags for each model. You can use simple text, pattern, callbacks.
- The component inject tags in the view.
- The view cell helps you to add or edit tags directly in the admin view of a record.

_Note: This plugin has been inspired by [CakePHP-Seo-Plugin](https://github.com/webtechnick/CakePHP-Seo-Plugin)_

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require orgasmicnightmare/cakephp-seo
```

Then update your database to create the new tables

```
bin/cake migrations migrate -p Seo
```

You can now access to the admin sections :
- yoursite/admin/seo/seo-uris
- yoursite/admin/seo/seo-titles
- yoursite/admin/seo/seo-meta-tags
- yoursite/admin/seo/seo-canonicals

## Behavior

The SeoBehavior allows you to automatize the creation of seo fields when you add a new entry.    

Configure the behavior, see below default configuration, and each time you will add an entry, all configured tags will be generated.
It's a real time saver…     

Later you can access to the data throw the admin sections or via the _Seo cell box_ if in you add it in your model view.

default configuration is :

```
$_defaultConfig = [
    'urls' => [
        [   
            'url' => [
                'prefix' => false,
                'action' => 'view',
                '_' => [
                    'slug' => 'slug'
                ]
            ],
            'title' => 'Seo default title',
            'canonical' => true,
            'meta_tags' => [
                'og:type' => [
                    'content' => 'website',
                    'is_property' => true
                ],
                'og:description' => [
                    'content' => '{{content}}',
                    'is_property' => true
                ],
                'og:locale' => [
                    'callback' => 'getLocale',
                    'is_property' => true
                ],
                'twitter:description' => [
                    'content' => '{{content}}',
                    'is_property' => true
                ],
            ]
        ]
    ]
];
```

- **urls** an array of urls you want to add tags
- **urls.url** standard CakePHP route options array. This key has two specials keys:
	- **_** used to pass some variable for the route ex : /brand/:slug 
	- **_callback** a callback to generate a route options array
- **urls.title** Title pattern. Ex: ```{{name}}```, will just use the field 'name' to fill the title tag.
- **urls.canonical** true if you want to add a canonical tag
- **urls.meta_tags** array of meta tags you want to add. Each key is a new tag name and support many options
	- **callback** a callback wich is executed to set the content of the tag (more on this later)
	- **content** a template to format the content. Ex : ```Hello {{name}}``` the text in the double accolade is a field of the entity.
	- **is_property** if true, the tag use the attribute "property" instead of "name".
	- **is_http_equiv** if true, the tag will have the attribute "is_http_equiv" set to true.

## Component

The SeoComponent prepare and send informations to the view based on the URI.   
To use it, just add it in your ```AppController.php``` or any child controllers.

default configuration is :

```
$_defaultConfig = [
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
```

- excludePrefix: bypass the process for theses prefixes
- defaults.title: the title tag
- defaults.prefix: prefix for the title tag
- defaults.suffix: suffix for the title tag

## View Cell

You have access to all your seo data throw the regular admin urls (ex: yoursite/admin/seo/seo-titles).   
But really it's not really helpfull and even say it… it's crappy

A cell view is here to help you to have all the seo information directly in the view you want.   
You have a product admin edit view.
in your Template/Admin/Products/edit.ctp, just add

```<?php
    $cell = $this->cell('Seo.Seobox', ['uri' => Router::url(['_name' => 'catalogView', $product->slugPath])]);
    echo $cell;
?>
```

In this example, I use a named route but you can use a standard array. **Just remember to use the route for which you want to add Seo metas, in this case the public view of a product**.

Actually, the default template doesn't respect the CakePHP default layout, and you surely want to override. To do this, [just follow the conventional way](http://book.cakephp.org/3.0/en/plugins.html#overriding-plugin-templates-from-inside-your-application). 
