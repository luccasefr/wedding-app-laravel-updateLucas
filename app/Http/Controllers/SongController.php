<?php

namespace App\Http\Controllers;

use App\Guest;
use App\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SongController extends Controller
{
    /**
     * @OA\post(
     *     path="/songs",
     *     tags={"Song"},
     *     summary="Create Song",
     *     description="Create a playlist of Songs to the wedding party",
     *     @OA\RequestBody(
     *         description="Song attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Song"),
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
            'name'=>'required',
            'artist'=>'required',
        ]);

        $guest = Guest::guest();
        $data=['user_id'=>$guest->user->id,'guest_id'=>$guest->id]+request()->all();
        return Song::create($data);
    }

    /**
     * @OA\get(
     *     path="/songs",
     *     tags={"Song"},
     *     summary="List of Songs",
     *     description="Return the playlist of all Songs",
     *     
     *    
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Song"),
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
        if(!$user){
            $user = Guest::guest()->user;
        }

        return $user->songs;
    }

    /**
     * @OA\delete(
     *     path="/song/{song}",
     *     tags={"Song"},
     *     summary="Delete Song",
     *     description="Delete Song of the playlist",
     *     @OA\Parameter(
     *         name="song",
     *         in="path",
     *         description="Song Id",
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
    public function delete(Song $song)
    {
        if(Auth::user()){
            if(Auth::user()->id!=$song->user->id){
                return response(['message'=>'you are not authorized to delete this song'],401);
            }

            $song->delete();
            return response(['message'=>'song deleted successfuly'],200);
        }

        $guest = Guest::guest();
        if($guest->user->id!=$song->user->id||$guest->id!=$song->guest->id){
            return response(['message'=>'you are not authorized to delete this song'],401);
        }

        $song->delete();
        return response(['message'=>'song deleted successfuly'],200);
    }

    /**
     * @OA\post(
     *     path="/song/{song}/like",
     *     tags={"Song"},
     *     summary="Song Likes",
     *     description="Save the Song Likes on pivot table",
     *     @OA\RequestBody(
     *         description="GuestLike attributes",
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
    public function like(Song $song)
    {
        $guest = Guest::guest();

        if($guest->user->id!=$song->user->id){
            return response(['message'=>'you are not authorized to like this song'],401);
        }

        if(count($song->guests_likes()->where('guest_id','=',$guest->id)->get())>0){
            $song->guests_likes()->detach($guest);
            return response(['message'=>'Song Unliked successful','guests_likes'=>$song->guests_likes],200);
        }

        $song->guests_likes()->attach($guest);
        return response(['message'=>'Song Liked successful','guests_likes'=>$song->guests_likes],200);
    }
}
