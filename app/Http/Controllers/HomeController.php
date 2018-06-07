<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Drawing;

class HomeController extends Controller
{

    /**
     * Load drawing, either editable or not 
     * 
     * @return \Illuminate\Http\Response
     */
    public function loadDrawing(Request $request, $slug, $name = 'Anonymous', $tripcode = null) {

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
                ->with('width', $drawing ? $drawing->width : false)
                ->with('height', $drawing ? $drawing->height : false)
                ->with('drawing', $drawing ? $drawing : false);
            
    }

    /**
     * Fetch just lines, this is fetched by the LIVE looped.
     * 
     * @return \Illuminate\Http\Response (json)
     */
    public function fetchDrawing(Request $request) {

        $request->validate([
            'slug' => 'required|String|max:30'
        ]);
    
        $requestData = $request->all();
        $drawing = Drawing::where('slug', $requestData['slug'])->first();
        if($drawing)
            return response()->json($drawing);

        return abort(404);
    
    }


    /**
     * Make drawing
     * 
     * @return \Illuminate\Http\Response
     */
    public function makeDrawing(Request $request) {

        $request->validate([
            'slug' => 'required|String|max:30',
            'name' => 'required|String|max:30',
            'trip' => 'nullable|String|max:30',
            'lines'=> 'required',
            'width'=> 'required|Integer',
            'height'=>'required|Integer',
        ]);
    
        $requestData = $request->all();
    
        $drawing = Drawing::where('slug', $requestData['slug'])->first();
    
        if($drawing && $drawing->session_token == $requestData['_token']) {
            // carry on.. but 
            // TO DO check they're not posting too quickly..
            
        }
        else if($drawing == null) {
            $drawing = new Drawing;
            $drawing->session_token = $requestData['_token'];
            $drawing->slug = $requestData['slug'];    
        }
        else {
            return response()->json(['error' => 'Session mismatch! You don\'t appear to own this drawing.'], 403); // Status code here
        }
    
        $legalDrawing = true;
    
        if($requestData['width'] > 6000 || $requestData['height'] > 6000)
            return response()->json(['error' => 'Drawing too large! Changes not saved!.'], 403); // Status code here
    
        foreach($requestData['lines'] as $line) {
    
            $counter = 0;        
            foreach($line as $point) {
    
                // the data is incorrect lengths
                if ( ( $counter == 0 && count($point) != 5 ) || 
                    ( $counter > 0 && count($point) != 2 ) ){
    
                        $legalDrawing = false;
                        break;
                }
    
                if(!$legalDrawing) break;
    
                $legalDrawing = $legalDrawing & is_numeric($point[0]);
                \Log::info($point[0]);
                \Log::info($legalDrawing ? 'true' : 'false');
    
                $legalDrawing = $legalDrawing & is_numeric($point[1]);
    
                if($counter == 0){
                    $legalDrawing = $legalDrawing & is_numeric($point[2]);
                    $legalDrawing = $legalDrawing & is_numeric($point[3][0]);
                    $legalDrawing = $legalDrawing & is_numeric($point[3][1]);
                    $legalDrawing = $legalDrawing & is_numeric($point[3][2]);
                    $legalDrawing = $legalDrawing & is_numeric($point[4]);
                }
    
    
                if($legalDrawing == false) break;
                
                $counter++;
            }
    
            if($legalDrawing == false)
                return response()->json(['error' => 'Bad data! Changes not saved!.'], 403); // Status code here
    
    
        }
    
        $drawing->ip = $request->ip();
        $drawing->name = $requestData['name'];
        $drawing->trip = $requestData['tripcode'] ? '!'.substr(crypt($requestData['tripcode'], $requestData['tripcode']),-10) : null;
        $drawing->lines = json_encode($requestData['lines']);
        $drawing->width = $requestData['width'];
        $drawing->height = $requestData['height'];
        $drawing->save();
    
        $requestData['saved'] = $drawing->updated_at->timestamp;
    
        return response()->json(
            $requestData
        );
    
    }


}
