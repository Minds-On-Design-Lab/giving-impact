<?php

use Phinx\Migration\AbstractMigration;

class AddFrequencyTypes extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->execute('CREATE TABLE plans (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            account_id int(11) DEFAULT NULL,
            created_at datetime DEFAULT NULL,
            updated_at datetime DEFAULT NULL,
            stripe_plan_id varchar(255) DEFAULT NULL,
            stripe_subscription_id varchar(255) DEFAULT NULL,
            currency varchar(5) DEFAULT NULL,
            donation_total int(11) DEFAULT NULL,
            frequency_type tinyint(1) DEFAULT \'0\',
            frequency_period tinyint(2) DEFAULT \'0\',
            donation_id int(11) DEFAULT NULL,
            plan_token varchar(255) DEFAULT NULL,
            PRIMARY KEY (id)
        )');
        $this->execute('alter table campaigns add column frequency_type tinyint(1) default 0');
        $this->execute('alter table campaigns add column frequency_period tinyint(2) default 0');
        $this->execute('alter table donations add column plan_id int(11) default 0');
        $this->execute('alter table donations add column canceled tinyint(1) default 0');

    }
}
