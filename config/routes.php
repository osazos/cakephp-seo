<?php
use Cake\Routing\Router;

Router::prefix('admin', function ($routes) {
	$routes->plugin('Seo', function($routes) {
		$routes->fallbacks('DashedRoute');
	});
});