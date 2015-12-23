<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "main";
$route['404_override'] = '';

$route['login']             = "main/login";
$route['logout']            = "main/logout";

$route['new_account']       = "main/new_account";

$route['forgot']            = 'main/forgot';
$route['forgot/(:any)']     = 'main/forgot_verify';

$route['admin/(:any)']      = 'admin/$1';

$route['account']               = 'account/index';
$route['account/edit/(:any)']   = 'account/edit_$1';

$route['diagnostics']           = 'diagnostics';

$route['(initiate_donation|donate)/(:any)/(checkout|complete|process|contact)'] = 'donate/$3';
$route['(initiate_donation|donate)/(:any)'] = 'donate/index';

// $route['donation/(:any)/complete'] = 'manage_donation/complete';
// $route['donation/(:any)'] = 'manage_donation/index';

$route['campaigns/(:any)/opportunities(/:num)?']        = "opportunities/index";
$route['campaigns/(:any)/opportunities/(:any)/donations.csv'] = "opportunities/export";
$route['campaigns/(:any)/opportunities/(:any)/donations/(:num)'] = "opportunities/donations";
$route['campaigns/(:any)/opportunities/(:any)/donations/(:any)/edit'] = "opportunities/donation_edit";
$route['campaigns/(:any)/opportunities/(:any)/donations/(:any)/cancel'] = "opportunities/donation_cancel";
$route['campaigns/(:any)/opportunities/(:any)/donations/(:any)/refund'] = "opportunities/donation_refund";
$route['campaigns/(:any)/opportunities/(:any)/donations/new'] = "opportunities/donation_edit";
$route['campaigns/(:any)/opportunities/(:any)/donations/(:any)'] = "opportunities/donation";
$route['campaigns/(:any)/opportunities/inactive']        = "opportunities/inactive";
$route['campaigns/(:any)/opportunities/new']            = "opportunities/new_opportunity";
$route['campaigns/(:any)/opportunities/(:any)/edit']    = "opportunities/edit";
$route['campaigns/(:any)/opportunities/opportunities.csv'] = "opportunities/go_export";
$route['campaigns/(:any)/opportunities/(:any)/supporters/(:any)/remove'] = "opportunities/supporters_remove";
$route['campaigns/(:any)/opportunities/(:any)/(:any)/(:num)?'] = "opportunities/$3";
$route['campaigns/(:any)/opportunities/(:any)/(:any)']  = "opportunities/$3";
$route['campaigns/(:any)/opportunities/(:any)']         = "opportunities/view";
$route['campaigns/(:any)/opportunities/(:any)/supporters/(:any)'] = "opportunities/supporters";


$route['campaigns/(:any)/donations.csv'] = "campaigns/export";
$route['campaigns/(:any)/donations/new'] = "campaigns/donation_edit";
$route['campaigns/(:any)/donations/(:num)'] = "campaigns/donations";
$route['campaigns/(:any)/donations/(:any)/edit'] = "campaigns/donation_edit";
$route['campaigns/(:any)/donations/(:any)/cancel'] = "campaigns/donation_cancel";
$route['campaigns/(:any)/donations/(:any)/refund'] = "campaigns/donation_refund";
$route['campaigns/(:any)/donations/(:any)'] = "campaigns/donation";
$route['campaigns/(:any)/(:any)/(:num)'] = "campaigns/$2";
$route['campaigns/(:any)/edit/(:any)']   = "campaigns/edit_$2";
$route['campaigns/(:any)/(:num)']        = "campaigns/view";
$route['campaigns/(:any)/(:any)']        = "campaigns/$2";
$route['campaigns/new']                  = "campaigns/new_campaign";
$route['campaigns/(:any)']               = "campaigns/view";

$route['supporters']               = "supporters/index";
$route['supporters/new']           = "supporters/new_supporter";
$route['supporters/(:num)?']       = "supporters/index";
$route['supporters/(:any)/edit']   = "supporters/edit";
$route['supporters/(:any)']        = "supporters/view";


/* End of file routes.php */
/* Location: ./application/config/routes.php */
