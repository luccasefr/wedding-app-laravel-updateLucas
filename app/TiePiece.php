<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class TiePiece extends Model
{
    

    /**
     * @OA\Schema(
     *     schema="TiePiece",
     *     required={"id", "value", "user_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="value",
     *         type="double",
     *         example=20.63
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="string",
     *         example="TRU87856"     
     *     )
     * )
    */ 
    protected $table = 'tie_pieces';
    protected $fillable=['value','user_id'];
    protected $dates=['created_at','updated_at'];

    public function tie_buy(){
        return $this->hasMany('App\TieBuy');
    }
}
