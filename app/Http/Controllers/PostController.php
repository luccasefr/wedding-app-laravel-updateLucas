<?php

namespace App\Http\Controllers;

use App\Guest;
use App\Post;
use App\User;
use App\PostsLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * @OA\post(
     *     path="/post",
     *     tags={"Post"},
     *     summary="Create Post",
     *     description="Create a Post with a filter containing censured words chosen by the couple and using the image of the Guest who commented",
     *     @OA\RequestBody(
     *         description="Post attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Post"),
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
            'text'=>'required|max:250',
            'guest_id'=>'required',
            'image'=>'image'
        ]);
        $data=request()->all();
        $user = Guest::find(request()->guest_id)->user;
        // return request()->image;
        if(!$user->ValidText(request()->text)){
            return response(['error'=>'the text containt a word that is not allowed'],403);
        }
        if(isset(request()->image)){
            $index=0;
            $imageUrl;
            do {
                $imageUrl=$user->id.'/posts-images/';
                $imageName = request()->guest_id.'-'.$index.'.'.request()->image->extension();
                $index++;
            } while (Storage::disk('local')->exists($imageUrl.$imageName)!=null);

            Storage::disk('local')->putFileAs($imageUrl, request()->image,$imageName);
            $data=['image_url'=>$imageUrl.$imageName]+$data;
        }

        return Post::create($data);
    }

    /**
     * @OA\delete(
     *     path="/post/{post}",
     *     tags={"Post"},
     *     summary="Delete Post",
     *     description="Delete Guest Post by User or if the Guest owns the post",
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="Post Id",
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
    public function delete(Post $post)
    {
        $user = Auth::user();
        if($user){
            if($user->id==$post->guest->user->id){
                $post->delete();
                return response(['message'=>'post deleted successful'],200);
            }
        }
        else if(Guest::guest()->id==$post->guest->id) {
            $post->delete();
            return response(['message'=>'post deleted successful'],200);
        }

        return response(['error'=>'You are not allowed to delete this post'],401);
    }

    /**
     * @OA\get(
     *     path="/user/posts-for-aprove",
     *     tags={"User"},
     *     summary="List of for Aprove Posts",
     *     description="Return the list of Posts to Aprove",
     *     @OA\RequestBody(
     *         description="Post attributes",
     *         required=true,
     *         
     *         
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Post"),
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
    public function forAprove()
    {
        return Auth::user()->posts()->filter(function($item){
            // print_r($item->aproved);
            return !$item->aproved;
        })->values();
    }

    /**
     * @OA\get(
     *     path="/posts",
     *     tags={"Post"},
     *     summary="List Posts",
     *     description="Return the list of Posts to user or aproved posts to the user's guests",
     *     
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Post"),
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
        $user = Auth::user();
        if($user){
            return Auth::user()->posts()->filter(function($item){
                return $item->aproved;
            })->sortByDesc('created_at')->values();
        }else {
            $guest = Guest::guest();
            $user = User::find($guest->user_id);

            return $user->posts()->filter(function($item){
                return $item->aproved;
            })->sortByDesc('created_at')->values();
        }
    }

    /**
     * @OA\post(
     *     path="/post/{post}/aprove",
     *     tags={"Post"},
     *     summary="Aprove the Post",
     *     description="Aprovall by the couple",
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="Post Id",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *     @OA\RequestBody(
     *         description="Post attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PostAprove"),
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
    public function aprove(Post $post)
    {
        if(Auth::user()->id==Guest::find($post->guest_id)->user_id){
            $post->aproved=true;
            $post->save();
            return $post;
        }else {
            return response(['error'=>'You are not allowed to change this post'],401);
        }
    }

    /**
     * @OA\post(
     *     path="/post/{post}/like",
     *     tags={"Post"},
     *     summary="Post Likes",
     *     description="Save the Post Likes on pivot table by post id",
     *     @OA\RequestBody(
     *         description="Post attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PostLike"),
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
    public function postLike(Post $post)
    {
        $guest = Guest::guest();
        if ($post->guest->user_id != $guest->user_id) {
            return response(['error'=>'You are not allowed like this post'],401);
        }

        foreach ($post->guests_likes as $postGuest) {
            if($guest->id==$postGuest->id){
                $guest->posts_likes()->detach($post);
                return response(['message'=>'Post Unliked successful'],200);
            }
        }

        $guest->posts_likes()->attach($post);

        return response(['message'=>'Post Liked successful'],200);
    }


    /**
     * @OA\post(
     *     path="/post/{post}/likes",
     *     tags={"Post"},
     *     summary="Post Likes by Guests",
     *     description="Save the Post Likes on pivot table by post id",
     *     @OA\RequestBody(
     *         description="Post attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GuestLike"),
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
    public function likes(Post $post)
    {
        $user = Auth::user();
        if($user){
            if($user->id==$post->guest->user->id){
                return $post->guests_likes;
            }
        }else {
            $guest = Guest::guest();

            if($guest->user->id==$post->guest->user->id){
                return $post->guests_likes;
            }
        }

        return response(['error'=>'You are not authorized to see this likes'],401);
        // return $post->guests_likes();
    }


    /**
     * @OA\get(
     *     path="/post/{post}/image",
     *     tags={"Invite"},
     *     summary="Show Postimage by id",
     *     description="Show Postimage by Id",
     *     @OA\Parameter(
     *         name="post",
     *         in="path",
     *         description="Post Id",
     *         required=true,
     *             @OA\Schema(
     *                 type="string"
     *             )
     *     ),
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/GuestLike"),
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
    public function getImage(Post $post)
    {
        $user = Auth::user();
        if($user){
            if($user->id==$post->guest->user->id){
                return response()->file('storage/app/'.$post->image_url);
            }
        }else {
            $guest = Guest::guest();

            if($guest->user->id==$post->guest->user->id){
                return response()->file('storage/app/'.$post->image_url);
            }
        }

        return response(['error'=>'You are not authorized to see this image'],401);
    }
}
