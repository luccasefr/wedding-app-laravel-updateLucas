<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class Gender extends Model
{
    //
    //
    /**
     * @OA\Schema(
     *     schema="Gender",
     *     required={"id", "name"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="female"
     *     )
     * )
    */  
}
