<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Invite;
use App\User;
use App\InviteImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InviteImageController extends Controller
{
    /**
     * @OA\post(
     *     path="/invite/image",
     *     tags={"Invite"},
     *     summary="Create Invite Image",
     *     description="Create Invite Image",
     *     @OA\RequestBody(
     *         description="InviteImage attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InviteImage"),
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
            'width'=>'required|numeric',
            'height'=>'required|numeric',
            'x'=>'required|numeric',
            'y'=>'required|numeric',
            'layer'=>'required|numeric',
        ]);

        $invite = Auth::user()->invite;
        if(!$invite){
            $invite = Auth::user()->invite()->save(new Invite());
        }

        $index=0;
        $imageUrl;
        do {
            $imageUrl=Auth::user()->id.'/invite-images/';
            $imageName ='invite_bg_img-'.$index.'.'.request()->image->extension();
            $index++;
        } while (Storage::disk('local')->exists($imageUrl.$imageName)!=null);

        Storage::disk('local')->putFileAs($imageUrl, request()->image,$imageName);
        $data=['image_url'=>$imageUrl.$imageName]+request()->all();
        return $invite->images()->create($data);

    }

    /**
     * @OA\delete(
     *     path="/invite/image/{image}",
     *     tags={"Invite"},
     *     summary="Delete Invite's Image",
     *     description="Delete Invite's Image by Invite's Id ",
     *     @OA\Parameter(
     *         name="text",
     *         in="path",
     *         description="InviteImage Id",
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
    public function delete(InviteImage $image)
    {
      if($image->invite->user->id!=Auth::user()->id){
        return response(['message'=>'you are not authorized to delete this image'],401);
      }
      Storage::disk('local')->delete($image->image_url);
      $image->delete();
      return response(['message'=>'image delete successfuly'],200);
    }

    /**
     * @OA\post(
     *     path="/invite/image/{image}",
     *     tags={"Invite"},
     *     summary="Update Invite Image",
     *     description="Update the InviteImage",
     *     @OA\Parameter(
     *         name="text",
     *         in="path",
     *         description="InviteImage Id",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *     @OA\RequestBody(
     *         description="InviteImage attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/InviteImage"),
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
    public function update(InviteImage $image)
    {
        if($image->invite->user->id!=Auth::user()->id){
          return response(['message'=>'you are not authorized to change this image'],401);
        }

        request()->validate([
            'width'=>'numeric',
            'height'=>'numeric',
            'x'=>'numeric',
            'y'=>'numeric',
            'layer'=>'numeric',
        ]);

        $image->width = request()->width ? request()->width : $image->width;
        $image->height = request()->height ? request()->height : $image->height;
        $image->x = request()->x ? request()->x : $image->x;
        $image->y = request()->y ? request()->y : $image->y;
        $image->layer = request()->layer ? request()->layer : $image->layer;

        $image->save();
        
        return $image;

    }

    /**
     * @OA\get(
     *     path="/invite/image/{image}",
     *     tags={"Post"},
     *     summary="Show InviteImage by id",
     *     description="Show InviteImage by Id",
     *     @OA\Parameter(
     *         name="image",
     *         in="path",
     *         description="InviteImage Id",
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
    public function getImage(InviteImage $image)
    {
        if(Auth::user()->id!=$image->invite->user->id){
            return response(['message'=>'you are not authorized to see this image'],401);
        }

        return response()->file('storage/app/'.$image->image_url);
    }
}
