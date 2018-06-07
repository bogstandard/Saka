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



Route::get('/', function () {

    for($complexity = 2; $complexity < 30; $complexity++) {
        $slug = '';
        $c  = 'bcdfghjklmnprstvwz'; //consonants except hard to speak ones
        $v  = 'aeiou';              //vowels
        $a  = $c.$v;                //both
    
        //use two syllables...
        for($i=0;$i < $complexity; $i++){
            $slug .= $c[rand(0, strlen($c)-1)];
            $slug .= $v[rand(0, strlen($v)-1)];
            $slug .= $a[rand(0, strlen($a)-1)];
        }

        $drawing = Drawing::where('slug', $slug)->first();
        if($drawing==null)
            return redirect('/'.$slug); // free space found, go to it

    }

    return redirect('/'); // incredible bad luck.. no spaces found, roll the 30 sided dice again.
    
});


Route::get('/{slug}/{name?}/{tripcode?}', 'HomeController@loadDrawing');

Route::post('/!make-drawing', 'HomeController@makeDrawing');
Route::post('/!fetch-drawing', 'HomeController@fetchDrawing');
