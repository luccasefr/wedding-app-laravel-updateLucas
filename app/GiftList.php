<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;


class GiftList extends Model
{
    //
    /**
     * @OA\Schema(
     *     schema="GiftList",
     *     required={"id", "name", "link", "user_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="Ricardo eletro"
     *     ),
     *     @OA\Property(
     *         property="link",
     *         type="string",
     *         example="http://ricardoeletro.com.br"    
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         example=1
     *     )
     * )
    */  
    protected $fillable=['user_id','link','name'];
}
