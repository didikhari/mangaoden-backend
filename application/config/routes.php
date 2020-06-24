<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* api */
$route['api/v1/home/slider'] = 'HomeController/slider';
$route['api/v1/home/hot'] = 'HomeController/comics/last_update_date';
$route['api/v1/home/favorite'] = 'HomeController/comics/hits';
$route['api/v1/comics'] = 'ComicController/comics';
$route['api/v1/comics/(:any)'] = 'ComicController/comicsDetail/$1';
$route['api/v1/comics/(:any)/chapters'] = 'ChapterController/chapters/$1';
$route['api/v1/comics/(:any)/chapters/(:any)'] = 'ChapterController/chapterDetail/$1/$2';

/* zeroscans */
$route['retriever/zeroscans/star-martial-god-technique'] = 'ZeroScansRetriever/manga/1';
$route['retriever/zeroscans/second-life-ranker'] = 'ZeroScansRetriever/manga/2';
$route['retriever/zeroscans/wind-sword'] = 'ZeroScansRetriever/manga/3';
$route['retriever/zeroscans/hero-i-quit-long-time-ago'] = 'ZeroScansRetriever/manga/4';

/* hatigarmscanz */
$route['retriever/hatigarmscanz/tales-of-demons-and-gods'] = 'HatigarmscanzRetriever/manga/5';
$route['retriever/hatigarmscanz/the-scholars-reincarnation'] = 'HatigarmscanzRetriever/manga/7';

/* earlymanga */
$route['retriever/earlymanga/master-of-legendary-realms'] = 'WPMangaRetriever/manga/6/3';
$route['retriever/earlymanga/the-last-human'] = 'WPMangaRetriever/manga/9/3';
$route['retriever/earlymanga/murim-login'] = 'WPMangaRetriever/manga/12/3';

/* manhuas */
$route['retriever/mangatx/the-demon-blades'] = 'WPMangaRetriever/manga/8/2';

/* kissmanga */
$route['retriever/kissmanga/master-of-gu'] = 'WPMangaRetriever/manga/10/2';

/* leviatanscans */
$route['retriever/leviatanscans/legend-of-the-northern-blade'] = 'LeviatanscansRetriever/manga/11';