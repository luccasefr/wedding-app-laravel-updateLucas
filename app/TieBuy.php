<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class TieBuy extends Model
{
    //
    
    /**
     * @OA\Schema(
     *     schema="TieBuy",
     *     required={"id", "tie_piece_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="tie_piece_id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="guest_id",
     *         type="string",
     *         example="TRU87856"     
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="Johnes"
     *     )
     * )
    */ 
    protected $table = 'tie_buys';
    protected $fillable=['tie_piece_id','guest_id','name'];
    protected $dates=['created_at','updated_at'];

    public function tie_piece()
    {
        return $this->belongsTo('App\TiePiece', 'tie_piece_id')->with('tie_piece');
    }

    public function guest()
    {
        return $this->belongsTo('App\Guest');
    }
}
