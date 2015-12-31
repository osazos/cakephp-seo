<?php
namespace Seo\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SeoUrisFixture
 *
 */
class SeoUrisFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'uri' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'is_approved' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'uri' => ['type' => 'unique', 'columns' => ['uri'], 'length' => []],
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
            'uri' => '/articles/view?slug=test-title-one',
            'is_approved' => 1,
            'created' => '2015-09-18 07:06:55',
            'modified' => '2015-09-18 07:06:55'
        ],
        [
            'id' => 2,
            'uri' => '/articles/view/test-title-two',
            'is_approved' => 0,
            'created' => '2015-09-18 07:06:55',
            'modified' => '2015-09-18 07:06:55'
        ],
        [
            'id' => 3,
            'uri' => '/articles/view/test-title-three',
            'is_approved' => 1,
            'created' => '2015-09-18 07:06:55',
            'modified' => '2015-09-18 07:06:55'
        ],
    ];
}
