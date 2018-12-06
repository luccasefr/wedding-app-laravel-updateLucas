<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class Post extends Model
{

    /**
     * @OA\Schema(
     *     schema="Post",
     *     required={"id", "text", "guest_id", "image_url", "aproved"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="text",
     *         type="string",
     *         example="Melhor Casal"
     *     ),
     *     @OA\Property(
     *         property="guest_id",
     *         type="string",
     *         example="TRU87856"     
     *     ),
     *     @OA\Property(
     *         property="image_url",
     *         type="string",
     *         example="post989820.png"
     *     ),
     *     @OA\Property(
     *         property="aproved",
     *         type="tinyint",
     *         example=1
     *     )
     * )
    */    
    protected $fillable=['guest_id','image_url','text','aproved'];

    public function guests_likes() {
        return $this->belongsToMany('App\Guest', 'posts_likes');
    }

    public function guest() {
        return $this->belongsTo('App\Guest');
    }
}
