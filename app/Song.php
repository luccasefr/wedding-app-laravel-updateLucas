<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class Song extends Model
{   

    /**
     * @OA\Schema(
     *     schema="Song",
     *     required={"id", "name", "artist", "likes", "guest_id", "user_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="Chocolate"
     *     ),
     *     @OA\Property(
     *         property="artist",
     *         type="string",
     *         example="Tim Maia"     
     *     ),
     *     @OA\Property(
     *         property="likes",
     *         type="integer",
     *         example=6
     *     ),
     *     @OA\Property(
     *         property="guest_id", 
     *         type="string",
     *         example="GRD87656"
     *     ),
     *     @OA\Property(
     *         property="user_id", 
     *         type="integer",
     *         example=1
     *     )
     * )
    */ 
    protected $fillable=['name','artist','guest_id','user_id'];

    public function guests_likes()
    {
        return $this->belongsToMany('App\Guest','songs_likes');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function guest()
    {
        return $this->belongsTo('App\Guest');
    }
}
