<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChromaImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ChromaImageController extends Controller
{
    /**
     * @OA\post(
     *     path="/chroma/image",
     *     tags={"ChromaImage"},
     *     summary="Create Chroma Image",
     *     description="Create Chroma image",
     *     @OA\RequestBody(
     *         description="Adding Chroma Image to Database",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ChromaImage"),
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
            'image'=>'required|image',
        ]);
        $data=['user_id'=>Auth::user()->id];
        $index=0;
        $imageUrl;
        do {
            $imageUrl=Auth::user()->id.'/chroma-images/';
            $imageName = $index.'.'.request()->image->extension();
            $index++;
        } while (Storage::disk('local')->exists($imageUrl.$imageName)!=null);

        Storage::disk('local')->putFileAs($imageUrl, request()->image,$imageName);
        $data+=['img_url'=>$imageUrl.$imageName];

        return ChromaImage::create($data);
    }

    /**
     * @OA\delete(
     *     path="/chroma/image{image}",
     *     tags={"ChromaImage"},
     *     summary="Delete Chroma Image",
     *     description="Delete Chroma image of the User",
     *     @OA\Parameter(
     *         name="image",
     *         in="path",
     *         description="Chroma Image of the User",
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
    public function delete(ChromaImage $image)
    {
        if(Auth::user()->id!=$image->user->id){
            return response(['message'=>'you are not authorized to delete this image'],401);
        }

        Storage::disk('local')->delete($image->img_url);
        $image->delete();
        return response(['message'=>'chroma image delete successful'],200);
    }

    /**
     * @OA\get(
     *     path="/chroma/image",
     *     tags={"ChromaImage"},
     *     summary="List Chroma Image",
     *     description="Return the List of Chroma image per User",
     *     parameters="",
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="array",
     *            @OA\Items(ref="#/components/schemas/ChromaImage")
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
    public function index()
    {
        return Auth::user()->chroma_images;
    }
}
