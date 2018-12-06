<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SensibleWord extends Model
{
    /**
     * @OA\Schema(
     *     schema="SensibleWord",
     *     required={"id", "word", "user_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="word",
     *         type="string",
     *         example="Sucesso"
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         example=1     
     *     )
     * )
    */
    protected $fillable=['word','user_id'];
}
