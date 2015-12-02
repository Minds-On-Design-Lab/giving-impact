<?php

use Phinx\Migration\AbstractMigration;

class CreateTransactions extends AbstractMigration
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
        $this->adapter->execute("CREATE TABLE transactions (
            id int(11) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime DEFAULT NULL,
            updated_at datetime DEFAULT NULL,
            stripe_id varchar(255) DEFAULT NULL,
            type varchar(255) DEFAULT NULL,
            total decimal(9,2) DEFAULT NULL,
            donation_id int(11) DEFAULT NULL,
            refunded tinyint(1) DEFAULT '0',
            PRIMARY KEY (id)
        )");


        $q = 'select id, created_at, amount, stripe_charge_id from donations';

        foreach( $this->fetchAll($q) as $row ) {
            if( !$row['stripe_charge_id'] ) {
                continue;
            }

            $q = 'insert into transactions set
                donation_id = "%s",
                created_at = "%s",
                updated_at = created_at,
                stripe_id = "%s",
                type = "charge",
                total = "%s"';
            $this->execute(sprintf(
                $q,
                $row['id'],
                $row['created_at'],
                $row['stripe_charge_id'],
                $row['amount']
            ));
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}