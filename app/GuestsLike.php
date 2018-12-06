<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class GuestsLike extends Model
{   

    /**
     * @OA\Schema(
     *     schema="GuestLike",
     *     required={"guest_id", "liked_id", "liked"},
     *     @OA\Property(
     *         property="guest_id",
     *         type="string",
     *         example="JCM88976"
     *     ),
     *     @OA\Property(
     *         property="liked_id",
     *         type="string",
     *         example="JMF87676"
     *     ),
     *     @OA\Property(
     *         property="liked",
     *         type="tinyint",
     *         example=1     
     *     )
     * )
    */ 
    protected $fillable=['guest_id','liked_id'];
    public $timestamps = false;

    public function guest()
    {
        return $this->belongsTo('App\Guest', 'guest_id', 'id');
    }

    public function guestLiked()
    {
        return $this->belongsTo('App\Guest', 'liked_id', 'id');
    }
}
