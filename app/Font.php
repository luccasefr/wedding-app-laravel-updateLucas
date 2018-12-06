<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    //
    //
    /**
     * @OA\Schema(
     *     schema="Font",
     *     required={"id", "name", "font_url"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="Arial"
     *     ),
     *     @OA\Property(
     *         property="font_url",
     *         type="string",
     *         example="Arial Black"     
     *     )
     * )
    */  
}
