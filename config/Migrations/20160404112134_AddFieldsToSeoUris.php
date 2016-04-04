<?php
use Migrations\AbstractMigration;

class AddFieldsToSeoUris extends AbstractMigration
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
        $table->addColumn('locale', 'string', ['limit' => '6', 'null' => true, 'default' => null, 'after' => 'uri']);
        $table->addColumn('foreign_key', 'integer', ['null' => true, 'default' => null, 'after' => 'uri']);
        $table->addColumn('model', 'string', ['limit' => 255, 'null' => true, 'default' => null, 'after' => 'uri']);
        $table->addIndex(['model', 'foreign_key', 'locale']);
        $table->update();
    }
}
