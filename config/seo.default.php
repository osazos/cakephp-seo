<?php

return [
    'Cache' => [
    	'seo' => [
            'className' => 'File',
            'prefix' => 'seo__',
            'path' => CACHE . 'views/',
            'duration' => '+1 months',
            'serialize' => true,
        ],
    ]
];