<?php

use Phinx\Migration\AbstractMigration;

class ConvertTotals extends AbstractMigration
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
        $this->execute('alter table campaigns add column minimum_donation_amount_2 int(11) default 0 after minimum_donation_amount');
        $this->execute('alter table campaigns add column target_2 bigint(20) default 0 after target');
        $this->execute('alter table campaigns add column current_2 bigint(20) default 0 after current');

        $q = 'select id, target, current, minimum_donation_amount from campaigns';

        foreach( $this->fetchAll($q) as $row ) {
            $update = array();
            if( $row['minimum_donation_amount'] ) {
                $update[] = 'minimum_donation_amount_2 = '.round($row['minimum_donation_amount'] * 100);
            }
            if( $row['target'] ) {
                $update[] = 'target_2 = '.round($row['target'] * 100);
            }
            if( $row['current'] ) {
                $update[] = 'current_2 = '.round($row['current'] * 100);
            }

            if( !count($update) ) {
                continue;
            }

            $this->execute('update campaigns set '.implode(', ', $update).' where id = '.$row['id'].' limit 1');
        }

        $this->execute('alter table campaigns drop column minimum_donation_amount');
        $this->execute('alter table campaigns drop column target');
        $this->execute('alter table campaigns drop column current');

        $this->execute('alter table campaigns change column minimum_donation_amount_2 minimum_donation_amount int(11) default 0');
        $this->execute('alter table campaigns change column target_2 target bigint(20) default 0');
        $this->execute('alter table campaigns change column current_2 current bigint(20) default 0');

        $this->execute('alter table donations add column amount_2 bigint(20) default 0 after amount');
        $this->execute('alter table donations add column original_amount_2 bigint(20) default 0 after original_amount');

        $q = 'select id, amount, original_amount from donations';

        foreach( $this->fetchAll($q) as $row ) {
            $update = array();
            if( $row['amount'] ) {
                $update[] = 'amount_2 = '.round($row['amount'] * 100);
            }
            if( $row['original_amount'] ) {
                $update[] = 'original_amount_2 = '.round($row['original_amount'] * 100);
            }

            if( !count($update) ) {
                continue;
            }

            $this->execute('update donations set '.implode(', ', $update).' where id = '.$row['id'].' limit 1');
        }

        $this->execute('alter table donations drop column amount');
        $this->execute('alter table donations drop column original_amount');

        $this->execute('alter table donations change column amount_2 amount bigint(20) default 0');
        $this->execute('alter table donations change column original_amount_2 original_amount bigint(20) default 0');

        $this->execute('alter table supporters add column donations_total_2 bigint(20) default 0 after donations_total');

        $q = 'select id, donations_total from supporters';

        foreach( $this->fetchAll($q) as $row ) {
            $update = array();
            if( $row['donations_total'] ) {
                $update[] = 'donations_total_2 = '.round($row['donations_total'] * 100);
            }

            if( !count($update) ) {
                continue;
            }

            $this->execute('update supporters set '.implode(', ', $update).' where id = '.$row['id'].' limit 1');
        }

        $this->execute('alter table supporters drop column donations_total');

        $this->execute('alter table supporters change column donations_total_2 donations_total bigint(20) default 0');

        $this->execute('alter table transactions add column total_2 bigint(20) default 0 after total');

        $q = 'select id, total from transactions';

        foreach( $this->fetchAll($q) as $row ) {
            $update = array();
            if( $row['total'] ) {
                $update[] = 'total_2 = '.round($row['total'] * 100);
            }

            if( !count($update) ) {
                continue;
            }

            $this->execute('update transactions set '.implode(', ', $update).' where id = '.$row['id'].' limit 1');
        }

        $this->execute('alter table transactions drop column total');

        $this->execute('alter table transactions change column total_2 total bigint(20) default 0');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}