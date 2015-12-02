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
$route['404_override'] = 'errors/missing';

$route["api/(v(:any)/)checkout"] = "v$2/checkout";

// [vN/]supporters
$route["api/(v(:any)/)supporters"] = "v$2/supporters";
$route["api/(v(:any)/)supporters(/:any)/donations"] = "v$2/supporters/donations$3";
$route["api/(v(:any)/)supporters(/:any)/opportunities"] = "v$2/supporters/opportunities$3";
$route["api/(v(:any)/)supporters(/:any)"] = "v$2/supporters/token$3";

// [vN/]stats
$route["api/(v(:any)/)campaigns(/:any)/stats/(:any)"] = "v$2/stats/$4";
$route["api/(v(:any)/)opportunities(/:any)/stats/(:any)"] = "v$2/stats/$4";

// [vN/]campaigns AND [vN/]campaigns/all|active|inactive
$route["api/(v(:any)/)campaigns"] = 'v$2/campaigns';
// [vN/]campaigns/TOKEN/donations|opportunities
$route["api/(v(:any)/)campaigns(/:any)(/(donations|opportunities))"] = 'v$2/campaigns$4$3';
// [vN/]campaigns/TOKEN
$route["api/(v(:any)/)campaigns(/:any)"] = 'v$2/campaigns/token$3';

// [vN/]opportunities
$route["api/(v(:any)/)opportunities"] = 'v$2/opportunities';
// [vN/]opportunities/TOKEN/donations
$route["api/(v(:any)/)opportunities(/:any)(/donations)"] = 'v$2/opportunities$4$3';
// [vN/]opportunities/TOKEN
$route["api/(v(:any)/)opportunities(/:any)"] = 'v$2/opportunities/token$3';

// [vN/]donations
$route["api/(v(:any)/)donations"] = "v$2/donations";
$route["api/(v(:any)/)donations(/:any)"] = "v$2/donations/token$3";

// $route["api/migrate/install"] = "migrate/install";

/* End of file routes.php */
/* Location: ./application/config/routes.php */