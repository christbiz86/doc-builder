<?php
//require_once('JobStreetScraper.php');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/phpinfo', function () {
    return view('phpinfo');
});

Route::get('/readdoc', 'DocumentController@index');
Route::get('/runtest', 'DocumentController@runResumeTest');
Route::get('/runtestRefactor', 'DocumentController@runResumeTestRefactored');
Route::get('/onlyOffice', 'DocumentController@onlyOffice');
Route::get('/documents/{filename}', 'DocumentController@getFileUrl');
Route::post('/callBack','DocumentController@callBack');

Route::get('/scrape', function () {
//    $scraper = new JobStreetScraper();
//    $scraper->login('william@staffondemand.sg', 'Client2017!');
//    $scrapedResponse = $scraper->search([
//    'skills' => ['Python'],
//    'page' => 3
//    ]);
//    $scraper->logout();
//    return response($scrapedResponse)->header('Content-Type', 'application/json');
});

Route::get('/scrape2', function () {
    $account = new \App\Http\Handlers\JobBoard\JobStreetAccount();
    $account->login('william@staffondemand.sg', 'Client2017!');
    $scraper = new \App\Http\Handlers\JobBoard\JobStreetTalentSearchHandler($account);
    $scraper->setPageNumber(2);
    $scraper->setSkills(['Python']);
    $scrapedResponse = $scraper->search();
    $account->logout();
    return response($scrapedResponse)->header('Content-Type', 'application/json');
});

Route::get('/jobpost', function () {
    $account = new \App\Http\Handlers\JobBoard\JobStreetAccount();
    $account->login('william@staffondemand.sg', 'Client2017!');
    $jobPost = new \App\Http\Handlers\JobBoard\JobStreetPostHandler($account);
    $jobPost->setPostID(6779254);
    $jobPost->postJob();
    $id = $jobPost->getPostID();
    $account->logout();
    return response(sprintf("new ID is %d", $id))->header('Content-Type', 'text/plain');
});
