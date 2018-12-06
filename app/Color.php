<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    //
    /**
     * @OA\Schema(
     *     schema="Color",
     *     required={"id", "r", "g", "b"},
     *     @OA\Property(
     *         property="id",
     *         type="string",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="r",
     *         type="integer",
     *         example=12
     *     ),
     *     @OA\Property(
     *         property="g",
     *         type="integer",
     *         example=15     
     *     ),
     *     @OA\Property(
     *         property="b",
     *         type="integer",
     *         example=49
     *     )
     * )
    */    
}
