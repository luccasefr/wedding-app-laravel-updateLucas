<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ChromaImage extends Model
{

    /**
     * @OA\Schema(
     *     schema="ChromaImage",
     *     required={"img_url", "user_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="img_url",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *              
     *     )
     * )
    */
    protected $fillable = ['img_url','user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
}


