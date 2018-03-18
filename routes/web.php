<?php
use Illuminate\Http\Request;
use \App\Drawing;

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


Route::get('/{slug}/{name?}/{tripcode?}', function (Request $request, $slug, $name = 'Anonymous', $tripcode = null) {

    $drawing = Drawing::where('slug', $slug)->first();

    if( ($drawing == null) || ($drawing && $drawing->session_token == csrf_token() ) )
        $editable = true;

    else
        $editable = false; 

        return view('canvas')
        ->with('editable',  $editable )
        ->with('slug', $slug)
        ->with('name', $drawing ? $drawing->name : $name)
        ->with('tripcode', $tripcode)
        ->with('trip', $drawing ? $drawing->trip : ($tripcode ? '!'.substr(crypt($tripcode, $tripcode),-10) : null) )
        ->with('lines', $drawing ? $drawing->lines : json_encode([]))
        ->with('drawing', $drawing ? $drawing : false);
        
});

Route::post('/!make-drawing', function(Request $request) {

    $requestData = $request->all();

    $drawing = Drawing::where('slug', $requestData['slug'])->first();

    if($drawing && $drawing->session_token == $requestData['_token']) {
        // carry on..
    }
    else if($drawing == null) {
        $drawing = new Drawing;
        $drawing->session_token = $requestData['_token'];
        $drawing->slug = $requestData['slug'];    
    }
    else {
        return response()->json(['error' => 'Session mismatch! You don\'t appear to own this drawing.'], 403); // Status code here
    }

    $drawing->name = $requestData['name'];
    $drawing->trip = $requestData['tripcode'] ? '!'.substr(crypt($requestData['tripcode'], $requestData['tripcode']),-10) : null;
    $drawing->lines = json_encode($requestData['lines']);
    $drawing->save();

    $requestData['saved'] = $drawing->updated_at->timestamp;
    //unset($requestData['_token']);
    return response()->json(
        $requestData
    );

});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');