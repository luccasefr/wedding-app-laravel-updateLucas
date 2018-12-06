<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class Invite extends Model
{   
    
    /**
     * @OA\Schema(
     *     schema="Invite",
     *     required={"id", "user_id", "bg_url"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="bg_url",
     *         type="string",
     *         example="jiasudoiua.jpeg"    
     *     )
     * )
    */
    protected $fillable = ['user_id', 'bg_url'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function texts()
    {
        return $this->hasMany('App\InviteText');
    }

    public function images()
    {
        return $this->hasMany('App\InviteImage');
    }
}
