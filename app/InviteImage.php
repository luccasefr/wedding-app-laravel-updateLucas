<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class InviteImage extends Model
{   

    /**
     * @OA\Schema(
     *     schema="InviteImage",
     *     required={"id", "image_url", "width", "height", "x", "y", "layer", "invite_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="image_url",
     *         type="string",
     *         example="jaosidoqwoiklsa.jpeg"
     *     ),
     *     @OA\Property(
     *         property="width",
     *         type="double",
     *         example=1.41     
     *     ),
     *     @OA\Property(
     *         property="height",
     *         type="double",
     *         example=850.06
     *     ),
     *     @OA\Property(
     *         property="x",
     *         type="double",
     *         example=25.69
     *     ),
     *     @OA\Property(
     *         property="y",
     *         type="double",
     *         example=103.56
     *              
     *     ),
     *     @OA\Property(
     *         property="layer",
     *         type="integer",
     *         example=10
     *     ),
     *     @OA\Property(
     *         property="invite_id",
     *         type="integer",
     *         example=2
     *     )
     * )
    */    
    protected $fillable = ['image_url', 'width', 'height', 'x', 'y', 'invite_id', 'layer'];

    public function invite()
    {
        return $this->belongsTo('App\Invite');
    }
}
