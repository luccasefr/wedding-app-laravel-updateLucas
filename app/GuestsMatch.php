<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class GuestsMatch extends Model
{
    //
    /**
     * @OA\Schema(
     *     schema="GuestMatch",
     *     required={"id","guest_id", "guest2_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example="1"
     *     ),
     *     @OA\Property(
     *         property="guest_id",
     *         type="string",
     *         example="JCM88976"
     *     ),
     *     @OA\Property(
     *         property="guest2_id",
     *         type="string",
     *         example="JMF87676"
     *     )
     *     
     * )
    */
}
