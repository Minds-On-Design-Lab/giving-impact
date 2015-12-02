<?php

use Phinx\Migration\AbstractMigration;

class UpdateDonations extends AbstractMigration
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
        $this->execute('alter table donations add column refunded tinyint(1) default 0');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}