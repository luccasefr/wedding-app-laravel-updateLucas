<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;

class InviteText extends Model
{

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     /**
     * @OA\Schema(
     *     schema="InviteText",
     *     required={"id", "text", "width", "height", "x", "y", "invite_id", "hexColor", "layer", "font_id", "font_size"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="text",
     *         type="string",
     *         example="Convite"
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
     *         property="invite_id",
     *         type="integer",
     *         example=10
     *     ),
     *     @OA\Property(
     *         property="hexColor",
     *         type="string",
     *         example="#000000"
     *     ),
     *     @OA\Property(
     *         property="layer",
     *         type="int",
     *         example=5
     *     ),
     *     @OA\Property(
     *         property="font_id",
     *         type="int",
     *         example=3
     *     ),
     *     @OA\Property(
     *         property="font_size",
     *         type="double",
     *         example=3.62
     *     )
     * )
    */
    protected $fillable = [
        'text', 'width', 'height', 'x', 'y', 'invite_id', 'layer', 'font_id', 'font_size','hexColor'
    ];

    public function invite()
    {
        return $this->belongsTo('App\Invite');
    }

    public function color()
    {
        return $this->belongsTo('App\Color');
    }

    public function font()
    {
        return $this->belongsTo('App\Font');
    }
}
