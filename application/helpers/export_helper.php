<?php
/**
 * Export helper
 */

/**
 * Convert Donation_model to CSV. Attempts to dump and capture directly
 * from STDOUT. If error occurs, that may also be trapped.
 *
 * @param  Object $campaign Campaign_model
 * @param  Object $rows     Database result
 * @return String
 */
function donations_to_csv($campaign, $rows) {
    ob_start();

    $CI = get_instance();

    $fp = fopen('php://output', 'w');
    $cols = array(
        'First Name',
        'Last Name',
        'Address',
        'City',
        'State',
        'Zip Code',
        'Email',
        'Contact',
        'Donation Date',
        'Donation Amount',
        'Donation Level Description',
        'Donation Level ID',
        'Campaign',
        'Giving Opportunity',
        'Transaction ID',
        'Offline Donation',
        'Refunded'
    );

    $field_ids = array();
    if( $campaign->custom_fields && count($campaign->custom_fields) ) {
        foreach( $campaign->custom_fields as $field ) {
            $cols[] = $field->field_label;
            $field_ids[] = $field->field_id;
        }
    }
    fputcsv($fp, $cols);

    foreach( $rows as $row ) {
        $opportunity = '';
        if( property_exists($row, 'opportunity') && $row->opportunity
            && property_exists($row->opportunity, 'title') ) {
            $opportunity = $row->opportunity->title;
        }

        $c = array(
            $row->first_name,
            $row->last_name,
            $row->billing_address1,
            $row->billing_city,
            $row->billing_state,
            $row->billing_postal_code,
            $row->email_address,
            $row->contact ? 1 : 0,
            date('m/d/Y',strtotime($row->donation_date)),
            number_format($row->donation_total/100, 2),
            $row->donation_level,
            $row->donation_level_id,
            $campaign->title,
            $opportunity,
            $row->id_token,
            $row->offline ? 1 : 0,
            $row->refunded ? 1 : 0
        );

        if( $campaign->custom_fields && count($campaign->custom_fields) ) {
            if( $row->custom_responses && count($row->custom_responses) ) {
                foreach( $row->custom_responses as $resp ) {
                    $c[] = $resp->response;
                }
            }
        }

        fputcsv($fp, $c);
    }

    fclose($fp);

    $out = ob_get_contents();
    ob_end_clean();

    return $out;
}

/**
 * Convert Donation_model to CSV. Attempts to dump and capture directly
 * from STDOUT. If error occurs, that may also be trapped.
 *
 * @param  Object $campaign Campaign_model
 * @param  Object $rows     Database result
 * @return String
 */
function opportunities_to_csv($campaign, $rows) {
    ob_start();

    $CI = get_instance();

    $fp = fopen('php://output', 'w');
    $cols = array(
        'ID Token',
        'Title',
        'Status',
        'Description',
        'Donation Target',
        'Donation Total',
        'Total Donations'
    );

    $field_ids = array();
    if( $campaign->campaign_fields && count($campaign->campaign_fields) ) {
        foreach( $campaign->campaign_fields as $field ) {
            $cols[] = $field->field_label;
            $field_ids[] = $field->field_id;
        }
    }

    fputcsv($fp, $cols);

    foreach( $rows as $row ) {

        $c = array(
            $row->id_token,
            $row->title,
            $row->status ? 1 : 0,
            $row->description,
            $row->donation_target,
            $row->donation_total,
            $row->total_donations
        );

        if( $row->campaign_responses && count($row->campaign_responses) ) {
            if( $row->campaign_responses && count($row->campaign_responses) ) {
                foreach( $row->campaign_responses as $resp ) {
                    $c[] = $resp->response;
                }
            }
        }

        fputcsv($fp, $c);
    }

    fclose($fp);

    $out = ob_get_contents();
    ob_end_clean();

    return $out;
}