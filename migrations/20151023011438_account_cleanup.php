<?php

use Phinx\Migration\AbstractMigration;

class AccountCleanup extends AbstractMigration
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
        $this->execute('alter table accounts drop column is_503c');
        $this->execute('alter table accounts drop column accepted_terms');
        $this->execute('alter table accounts drop column beta_features');

        $this->execute('alter table campaigns drop column fb_shares');
        $this->execute('alter table campaigns drop column twitter_shares');

        $this->execute('alter table donations drop column fb_share');
        $this->execute('alter table donations drop column twitter_share');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}