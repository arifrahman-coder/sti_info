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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'home';
$route['frontend/dashboard/simpan'] = 'frontend/dashboard/simpan';
$route['frontend/hasil-kuesioner'] = 'frontend/hasil_kuesioner/index';
$route['frontend/kuesioner-mars'] = 'frontend/kuesioner_mars/index';
$route['frontend/kuesioner-mars/simpan'] = 'frontend/kuesioner_mars/save';
$route['frontend/interaksi-obat'] = 'frontend/interaksi_obat/index';
$route['frontend/profil-penggunaan-obat'] = 'frontend/profil_penggunaan_obat/index';
$route['frontend/profil-penggunaan-obat/simpan'] = 'frontend/profil_penggunaan_obat/save';
$route['frontend/modul-pembelajaran/mulai/(:num)'] = 'frontend/modul_pembelajaran/mulai/$1';
$route['frontend/modul-pembelajaran/tes/(:num)/(pre_test|post_test)'] = 'frontend/modul_pembelajaran/tes/$1/$2';
$route['frontend/modul-pembelajaran/tes/(:num)/(pre_test|post_test)/simpan'] = 'frontend/modul_pembelajaran/simpanTes/$1/$2';
$route['backend/modul-pembelajaran'] = 'backend/modul_pembelajaran/index';
$route['backend/modul-pembelajaran/create'] = 'backend/modul_pembelajaran/createModule';
$route['backend/modul-pembelajaran/update/(:num)'] = 'backend/modul_pembelajaran/updateModule/$1';
$route['backend/modul-pembelajaran/delete/(:num)'] = 'backend/modul_pembelajaran/deleteModule/$1';
$route['backend/modul-pembelajaran/manage/(:num)'] = 'backend/modul_pembelajaran/manage/$1';
$route['backend/modul-pembelajaran/detail/create'] = 'backend/modul_pembelajaran/createDetail';
$route['backend/modul-pembelajaran/detail/update/(:num)'] = 'backend/modul_pembelajaran/updateDetail/$1';
$route['backend/modul-pembelajaran/detail/delete/(:num)'] = 'backend/modul_pembelajaran/deleteDetail/$1';
$route['backend/modul-pembelajaran/content/create'] = 'backend/modul_pembelajaran/createContent';
$route['backend/modul-pembelajaran/content/update/(:num)'] = 'backend/modul_pembelajaran/updateContent/$1';
$route['backend/modul-pembelajaran/content/delete/(:num)'] = 'backend/modul_pembelajaran/deleteContent/$1';
$route['backend/data-kuesioner-responden'] = 'backend/data_demografi_responden/index';
$route['backend/data-kuesioner-responden/export'] = 'backend/data_demografi_responden/export';
$route['backend/data-kuesioner-responden/detail/(:num)'] = 'backend/data_demografi_responden/detail/$1';
$route['backend/data-kuesioner-responden/edit/(:num)'] = 'backend/data_demografi_responden/edit/$1';
$route['backend/data-kuesioner-responden/update/(:num)'] = 'backend/data_demografi_responden/update/$1';
$route['backend/data-kuesioner-responden/bulk-delete'] = 'backend/data_demografi_responden/bulkDelete';
$route['backend/data-kuesioner-responden/delete/(:num)'] = 'backend/data_demografi_responden/delete/$1';
$route['backend/kuesioner-mars'] = 'backend/kuesioner_mars/index';
$route['backend/kuesioner-mars/create'] = 'backend/kuesioner_mars/create';
$route['backend/kuesioner-mars/store'] = 'backend/kuesioner_mars/store';
$route['backend/kuesioner-mars/detail/(:num)'] = 'backend/kuesioner_mars/detail/$1';
$route['backend/kuesioner-mars/edit/(:num)'] = 'backend/kuesioner_mars/edit/$1';
$route['backend/kuesioner-mars/update/(:num)'] = 'backend/kuesioner_mars/update/$1';
$route['backend/kuesioner-mars/delete/(:num)'] = 'backend/kuesioner_mars/delete/$1';
$route['backend/profil-penggunaan-obat'] = 'backend/profil_penggunaan_obat/index';
$route['backend/profil-penggunaan-obat/detail/(:num)'] = 'backend/profil_penggunaan_obat/detail/$1';
$route['backend/profil-penggunaan-obat/edit/(:num)'] = 'backend/profil_penggunaan_obat/edit/$1';
$route['backend/profil-penggunaan-obat/update/(:num)'] = 'backend/profil_penggunaan_obat/update/$1';
$route['backend/profil-penggunaan-obat/delete/(:num)'] = 'backend/profil_penggunaan_obat/delete/$1';
$route['backend/interaksi-obat'] = 'backend/interaksi_obat/index';
$route['backend/interaksi-obat/tambah'] = 'backend/interaksi_obat/create';
$route['backend/interaksi-obat/simpan'] = 'backend/interaksi_obat/store';
$route['backend/interaksi-obat/edit/(:num)'] = 'backend/interaksi_obat/edit/$1';
$route['backend/interaksi-obat/update/(:num)'] = 'backend/interaksi_obat/update/$1';
$route['backend/interaksi-obat/hapus/(:num)'] = 'backend/interaksi_obat/delete/$1';
// Alias lama dipertahankan agar bookmark admin yang sudah ada tetap berfungsi.
$route['backend/data-demografi-responden'] = 'backend/data_demografi_responden/index';
$route['backend/data-demografi-responden/export'] = 'backend/data_demografi_responden/export';
$route['backend/data-demografi-responden/detail/(:num)'] = 'backend/data_demografi_responden/detail/$1';
$route['backend/data-demografi-responden/edit/(:num)'] = 'backend/data_demografi_responden/edit/$1';
$route['backend/data-demografi-responden/update/(:num)'] = 'backend/data_demografi_responden/update/$1';
$route['backend/data-demografi-responden/bulk-delete'] = 'backend/data_demografi_responden/bulkDelete';
$route['backend/data-demografi-responden/delete/(:num)'] = 'backend/data_demografi_responden/delete/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
