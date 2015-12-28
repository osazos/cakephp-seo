<?php
use Migrations\AbstractMigration;

class InitSeo extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('seo_uris');
        $table->addColumn('uri', 'string')
            ->addColumn('is_approved', 'boolean', ['null' => true, 'default' => 1])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['uri'], ['unique' => true])
            ->save();

        $table = $this->table('seo_titles');
        $table->addColumn('seo_uri_id', 'integer')
            ->addColumn('title', 'string', ['limit' => 255])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['seo_uri_id'])
            ->save();

        $table = $this->table('seo_meta_tags');
        $table->addColumn('seo_uri_id', 'integer')
            ->addColumn('name', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('content', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('is_http_equiv', 'boolean', ['null' => true, 'default' => 0])
            ->addColumn('is_property', 'boolean', ['null' => true, 'default' => 0])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['seo_uri_id'])
            ->save();

        $table = $this->table('seo_canonicals');
        $table->addColumn('seo_uri_id', 'integer')
            ->addColumn('canonical', 'string', ['limit' => 255])
            ->addColumn('active', 'boolean', ['null' => true, 'default' => false])
            ->addIndex(['seo_uri_id'])
            ->save();

    }
}
