<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class MatchesConversation extends Model
{   

    
    /**
     * @OA\Schema(
     *     schema="MatchesConversation",
     *     required={"id","guest_id", "match_id", "message"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="guest_id",
     *         type="string",
     *         example="JCM88976"
     *     ),
     *     @OA\Property(
     *         property="match_id",
     *         type="string",
     *         example="JMF87676"
     *     ),
     *     @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Boa noite"     
     *     )
     * )
    */
    public function guest()
    {
        return $this->belongsTo('App\Guest');
    }

    public function guest2()
    {
        return $this->belongsTo('App\Guest', 'macth_id');
    }
}
