<?php
use Illuminate\Http\Request;
use \App\Drawing;
use \Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Auth::routes();

//Route::get('/home', 'HomeController@index')
//    ->name('home');

$toDie = Drawing::where('created_at', '<=', Carbon::now()->subDays(7)->toDateTimeString())->get();
foreach($toDie as $drawingToDie)
    $drawingToDie->delete();


Route::get('/', 'HomeController@newDrawing');
Route::get('/{slug}/{name?}/{tripcode?}', 'HomeController@loadDrawing');


Route::post('/!make-drawing', 'HomeController@makeDrawing');
Route::post('/!fork-drawing', 'HomeController@makeDrawing');
Route::post('/!fetch-drawing', 'HomeController@fetchDrawing');
