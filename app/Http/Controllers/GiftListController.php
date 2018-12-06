<?php

namespace App\Http\Controllers;

use App\GiftList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftListController extends Controller
{
    /**
     * @OA\post(
     *     path="/user/gift",
     *     tags={"User"},
     *     summary="Create the Gift List",
     *     description="Create the Gift List of the couple",
     *     @OA\RequestBody(
     *         description="List of method itens",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GiftList"),
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/GiftList"),
     *         
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplier"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     */
    public function create()
    {
        request()->validate([
            'name'=>'required',
            'link'=>'required'
        ]);

        return GiftList::create(['user_id'=>Auth::user()->id]+request()->all());
    }

    /**
     * @OA\delete(
     *     path="/gift/{gift}",
     *     tags={"User"},
     *     summary="Delete a Gift list",
     *     description="Delete a Gift List",
     *     @OA\Parameter(
     *         name="gift",
     *         in="path",
     *         description="Id of GiftList",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
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
    public function delete(GiftList $gift)
    {
        if(Auth::user()->id!=$gift->user_id){
            return response(['message'=>'you are not authorized to delete this gift list'],401);
        }

        $gift->delete();
        return response(['message'=>'Gift list deleted successful'],200);
    }

    /**
     * @OA\get(
     *     path="/user/gifts",
     *     tags={"User"},
     *     summary="Return the List of Gift LIst",
     *     description="Return the List of Gift LIst by User",
     *     parameters="",
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="array",
     *            @OA\Items(ref="#/components/schemas/GiftList"),
     *         ),
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
    public function getAll()
    {
        return Auth::user()->gift_lists;
    }
}
