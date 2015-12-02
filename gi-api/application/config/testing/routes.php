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
$_default = DEFAULT_API_VERSION;

$route['default_controller'] = "main";
$route['404_override'] = '';

// [vN/]stats
$route["(v(:any)/)campaigns(/:any)/stats/(:any)"] = "v$2/stats/$4";
$route["(v(:any)/)opportunities(/:any)/stats/(:any)"] = "v$2/stats/$4";

// [vN/]campaigns AND [vN/]campaigns/all|active|inactive
$route["(v(:any)/)campaigns"] = 'v$2/campaigns';
// [vN/]campaigns/TOKEN/donations|opportunities
$route["(v(:any)/)campaigns(/:any)(/(donations|opportunities))"] = 'v$2/campaigns$4$3';
// [vN/]campaigns/TOKEN
$route["(v(:any)/)campaigns(/:any)"] = 'v$2/campaigns/token$3';

// [vN/]opportunities
$route["(v(:any)/)opportunities"] = 'v$2/opportunities';
// [vN/]opportunities/TOKEN/donations
$route["(v(:any)/)opportunities(/:any)(/donations)"] = 'v$2/opportunities$4$3';
// [vN/]opportunities/TOKEN
$route["(v(:any)/)opportunities(/:any)"] = 'v$2/opportunities/token$3';

// [vN/]donations
$route["(v(:any)/)donations"] = "v$2/donations";
$route["(v(:any)/)donations(/:any)"] = "v$2/donations/token$3";

/* End of file routes.php */
/* Location: ./application/config/routes.php */