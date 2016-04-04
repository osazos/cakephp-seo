<?php
use Migrations\AbstractMigration;

class AddForeignkeyToSeoTables extends AbstractMigration
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

        $table = $this->table('seo_titles');
        $table->addForeignKey('seo_uri_id', 'seo_uris', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'));
        $table->update();

        $table = $this->table('seo_meta_tags');
        $table->addForeignKey('seo_uri_id', 'seo_uris', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'));
        $table->update();


        $table = $this->table('seo_canonicals');
        $table->addForeignKey('seo_uri_id', 'seo_uris', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'));
        $table->update();
    }
}
