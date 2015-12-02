<?php
$config = array(
    'donation' => array(
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'amount',
            'label' => 'Amount',
            'rules' => 'required|numeric|greater_than[0.00]'
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|email'
        ),
    ),

    'opportunity' => array(
        array(
            'field' => 'title',
            'label' => 'Opportunity Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'required'
        ),
        array(
            'field' => 'status',
            'label' => 'Status',
            'rules' => 'required'
        ),
        array(
            'field' => 'target',
            'label' => 'Target',
            'rules' => 'numeric|greater_than[0.00]'
        ),
    ),

    'campaign_basic' => array(
        array(
            'field' => 'title',
            'label' => 'Title',
            'rules' => 'required'
        ),
        array(
            'field' => 'target',
            'label' => 'Donation Target',
            'rules' => 'required|numeric|greater_than[5.00]'
        ),
        array(
            'field' => 'minimum',
            'label' => 'Minimum Donation',
            'rules' => 'required|numeric|greater_than[4.99]'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'required'
        )
    ),

    'campaign_receipt' => array(
        array(
            'field' => 'email_org_name',
            'label' => 'Organization Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'reply_to_address',
            'label' => 'Reply-to Address',
            'rules' => 'required|email'
        )
    ),

    'user_edit' => array(
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|email'
        ),
        array(
            'field' => 'pass',
            'label' => 'Password',
            'rules' => 'matches[pass2]'
        )
    ),

    'supporter' => array(
        array(
            'field' => 'first_name',
            'label' => 'First Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'Last Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|email'
        )
    )
);