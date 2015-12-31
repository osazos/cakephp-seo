<?php
namespace Seo\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ArticlesFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'title' => ['type' => 'string', 'null' => false, 'default' => null],
        'slug' => ['type' => 'string', 'null' => false, 'default' => null],
        'content' => ['type' => 'text', 'null' => false, 'default' => null],
        'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
        'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ];
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '1',
            'title' => 'Test title one',
            'slug' => 'test-title-one',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni esse odit fugiat, officiis tempore, numquam excepturi, vitae pariatur maxime, alias est quaerat consequatur vel cum exercitationem sint ex ab hic.',
            'created' => '2012-12-12 12:12:12',
            'modified' => '2013-01-01 11:11:11',
        ]
    ];
}