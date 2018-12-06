<?php

namespace App\Http\Controllers;

use App\SensibleWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SensibleWordController extends Controller
{
    /**
     * @OA\post(
     *     path="/user/sensible-word",
     *     tags={"User"},
     *     summary="Create sensible word",
     *     description="Create sensible word to filter the Posts",
     *     @OA\RequestBody(
     *         description="Sensible Words attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SensibleWord"),
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
        request()->validate([
            'word'=>'required'
        ]);

        return SensibleWord::create(request()->all()+['user_id'=>Auth::user()->id]);
    }

    /**
     * @OA\delete(
     *     path="/sensible-word/{sensibleWord}",
     *     tags={"User"},
     *     summary="Delete Sensible Words",
     *     description="Delete Sensible Words by User",
     *     @OA\Parameter(
     *         name="sensibleWord",
     *         in="path",
     *         description="SensibleWord Id",
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
    public function delete(SensibleWord $sensibleWord)
    {
        if(Auth::user()->id!=$sensibleWord->user_id){
            return response(['message'=>'you are not authorized to delete this word'],401);
        }

        $sensibleWord->delete();
        return response(['message'=>'Sensible word deleted successful'],200);
    }

    /**
     * @OA\get(
     *     path="/user/sensible-words",
     *     tags={"User"},
     *     summary="List Posts",
     *     description="Return the list of Posts to user or aproved posts to the user's guests",
     *     
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/SensibleWord"),
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
        return Auth::user()->sensible_words;
    }
}
