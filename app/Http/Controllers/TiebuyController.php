<?php

namespace App\Http\Controllers;
use App\TieBuy;
use App\TiePiece;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiebuyController extends Controller
{
    //
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\get(
     *     path="/tiebuy",
     *     tags={"TieBuy"},
     *     summary="List of TieBuy",
     *     description="Return the List of TieBuys",
     *     
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TieBuy"),
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

        $sql = "SELECT 
                tb.name as name, tp.value as value
            FROM 
                tie_pieces tp 
                INNER JOIN tie_buys tb ON tb.tie_piece_id = tp.id";

        

        return DB::select($sql);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\post(
     *     path="/tiebuy",
     *     tags={"TieBuy"},
     *     summary="Create TieBuy",
     *     description="Create a TieBuy of Guests",
     *     @OA\RequestBody(
     *         description="Tie Buy attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TieBuy"),
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
            'tie_piece_id'=>'required',
            'name'=>'required'
        ]);

        return TiePiece::create(['guest_id'=>Auth::guest()->id]+request()->all());
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
