<?php

use Phinx\Migration\AbstractMigration;

class CreateOppSupporterLookups extends AbstractMigration
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
        $this->query('CREATE TABLE opportunities_supporters (
          id int(11) unsigned NOT NULL AUTO_INCREMENT,
          supporter_id int(11) DEFAULT NULL,
          opportunity_id int(11) DEFAULT NULL,
          is_lead tinyint(1) DEFAULT 0,
          PRIMARY KEY (id)
        )');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}