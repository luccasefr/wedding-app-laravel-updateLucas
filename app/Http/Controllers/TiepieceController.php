<?php

namespace App\Http\Controllers;
use App\TiePiece;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiepieceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     /**
     * @OA\get(
     *     path="/tiepiece",
     *     tags={"TiePiece"},
     *     summary="List of TiePieces",
     *     description="Return the List of Tipieces",
     *     
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TiePiece"),
     *     ),
     *     
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     * 
     */
    public function index()
    {
        //
        return TiePiece::All();
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     /**
     * @OA\post(
     *     path="/tiepieces",
     *     tags={"TiePiece"},
     *     summary="Create TiePiece",
     *     description="Create a Piepiece for the Guests",
     *     @OA\RequestBody(
     *         description="TiePiece attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TiePiece"),
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         
     *         
     *     ),
     *     
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     * 
     */
    public function create()
    {
        //
        request()->validate([
            'value'=>'required'
        ]);

        return TiePiece::create(['user_id'=>Auth::user()->id]+request()->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        
        TiePiece::create($request->all());
        return $request;

        // $tipiece = new TiePiece();
        // $tipiece->value = $request->value;
        // $tipiece->user_id = $request->user_id;
        // $tipiece->save();
        // return $tipiece;
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $tipiece = TiePiece::findOrFail($id);
        return $tipiece;

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $tiepiece = TiePiece::findOrFail($id);
        $tiepiece->value = $request->value;
        $tiepiece->user_id = $request->user_id;
        $tiepiece->save();
        return $tipiece;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
        $tiepiece = TiePiece::findOrFail($id);
        $tiepiece->delete();
        return $tiepiece;
    }
}
