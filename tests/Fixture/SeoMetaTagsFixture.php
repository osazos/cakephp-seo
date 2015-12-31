<?php
namespace Seo\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SeoMetaTagsFixture
 *
 */
class SeoMetaTagsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'seo_uri_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'name' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'content' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'is_http_equiv' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null],
        'is_property' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'seo_uri_id' => 1,
            'name' => 'description',
            'content' => 'Seo description content',
            'is_http_equiv' => 0,
            'is_property' => 0,
            'created' => '2015-09-18 07:07:12',
            'modified' => '2015-09-18 07:07:12'
        ],
        [
            'id' => 2,
            'seo_uri_id' => 1,
            'name' => 'robots',
            'content' => 'index, follow',
            'is_http_equiv' => 0,
            'is_property' => 0,
            'created' => '2015-09-18 07:07:12',
            'modified' => '2015-09-18 07:07:12'
        ],
        [
            'id' => 3,
            'seo_uri_id' => 1,
            'name' => 'og:title',
            'content' => 'Open graph Seo Title',
            'is_http_equiv' => 0,
            'is_property' => 1,
            'created' => '2015-09-18 07:07:12',
            'modified' => '2015-09-18 07:07:12'
        ],
        [
            'id' => 4,
            'seo_uri_id' => 1,
            'name' => 'Content-Language',
            'content' => 'fr_FR',
            'is_http_equiv' => 1,
            'is_property' => 0,
            'created' => '2015-09-18 07:07:12',
            'modified' => '2015-09-18 07:07:12'
        ],
        [
            'id' => 5,
            'seo_uri_id' => 1,
            'name' => 'tag-name',
            'content' => '',
            'is_http_equiv' => 0,
            'is_property' => 0,
            'created' => '2015-09-18 07:07:12',
            'modified' => '2015-09-18 07:07:12'
        ],
        [
            'id' => 6,
            'seo_uri_id' => 1,
            'name' => 'tag-name',
            'content' => null,
            'is_http_equiv' => 0,
            'is_property' => 0,
            'created' => '2015-09-18 07:07:12',
            'modified' => '2015-09-18 07:07:12'
        ]
    ];
}
