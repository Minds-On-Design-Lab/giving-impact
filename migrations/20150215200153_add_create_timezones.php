<?php

use Phinx\Migration\AbstractMigration;

class AddCreateTimezones extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('alter table accounts add column timezone varchar(255) default "US/Eastern"');

    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}