<?php
class Commands extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->model('Donation_model');
		$this->load->model('Account_model');
        $this->load->helper('number');
        $this->load->helper('notification');

        /** VERY IMPORTANT - only turn this on when you need it. */
        // show_error('Access to this controller is blocked, turn me on when you need me.');
        // exit;
	}

	public function receipts() {
		$ids = 'e1992cb14a';

		$parts = explode(',', $ids);

		$donations = $this->Donation_model
			->where('donation_token = "'.implode('" or donation_token ="', $parts).'"')
			->find();

		foreach( $donations as $donation ) {
            $c = $donation->opportunity ? $donation->opportunity : $donation->campaign;
            $account = $this->Account_model->get_entry($donation->account_id);
            /**
             * UNCOMMENT THE FOLLOWING LINE TO ACTUALLY DO THE RECEIPT SENDING
             */
            notify_receipt($donation, $c, $account);
		}

	}
}
