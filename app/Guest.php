<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{

    /**
     * @OA\Schema(
     *     schema="Guest",
     *     required={"id", "user_id", "confirmed", "is_on_singles_meeting", "is_user"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="John"     
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string",
     *         example="john@email.com"
     *     ),
     *     @OA\Property(
     *         property="phone",
     *         type="string",
     *         example="(031)98484-5959"
     *     ),
     *     @OA\Property(
     *         property="age",
     *         type="integer",
     *         example=45
     *              
     *     ),
     *     @OA\Property(
     *         property="confirmed",
     *         type="tinyint",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="is_on_singles_meeting",
     *         type="tinyint",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="profile_img",
     *         type="string",
     *         example="minhafoto675.png"
     *              
     *     ),
     *     @OA\Property(
     *         property="photo1_url",
     *         type="string",
     *         example="2/CR84460/image/CR84460-image1.jpeg"
     *              
     *     ),
     *     @OA\Property(
     *         property="photo2_url",
     *         type="string",
     *         example="2/CR84460/image/CR84460-image2.jpeg"
     *              
     *     ),
     *     @OA\Property(
     *         property="photo3_url",
     *         type="string",
     *         example="2/CR84460/image/CR84460-image3.jpeg"
     *              
     *     ),
     *     @OA\Property(
     *         property="is_user",
     *         type="tinyint",
     *         example=1
     *              
     *     ),
     *     @OA\Property(
     *         property="about",
     *         type="string",
     *         example="About"
     *              
     *     ),
     *     @OA\Property(
     *         property="gender_id",
     *         type="integer",
     *         example=1
     *              
     *     ),
     *     @OA\Property(
     *         property="want_gender_id",
     *         type="integer",
     *         example=2
     *              
     *     ),
     *     @OA\Property(
     *         property="fcm_device_token",
     *         type="string",
     *         example="ioqweosjahsdipasd1287HSUa"
     *              
     *     )
     * )
    */    
    protected $fillable=['id','user_id','email','phone','name','is_on_singles_meeting','gender_id','want_gender_id','age','profile_img','photo1_url','photo2_url','photo3_url','about','fcm_device_token'];
    protected $primaryKey = 'id';
    public $incrementing = false;

    public static function generateId($sulfix)
    {
        $id;
        do {
            $id=$sulfix.rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
        } while (!is_null(Guest::find($id)));
        return $id;
    }

    public static function guest(){
        return Guest::find(request()->header('GuestAuthorization'));
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function posts()
    {
        return $this->hasMany('App\Post')->with('guest','guests_likes');
    }

    public function events()
    {
        return $this->belongsToMany('App\Event')->withPivot('confirmed');
    }

    public function posts_likes()
    {
        return $this->belongsToMany('App\Post', 'posts_likes');
    }

    public function guests_liked()
    {
        return $this->belongsToMany('App\Guest', 'guests_likes','guest_id','liked_id')->withPivot('liked');
    }

    public function guests_that_like_me()
    {
        return $this->belongsToMany('App\Guest', 'guests_likes','liked_id','guest_id')->withPivot('liked');
    }

    public function matchs1()
    {
        return $this->belongsToMany('App\Guest', 'guests_matches','guest_id','guest2_id');
    }

    public function matchs2()
    {
        return $this->belongsToMany('App\Guest', 'guests_matches','guest2_id','guest_id');
    }

    public function matchs()
    {
         return $this->matchs1->merge($this->matchs2);
    }

    public function matches_conversations()
    {
        return $this->hasMany('App\MatchesConversation');
    }

    public function matches_conversations2()
    {
        return $this->hasMany('App\MatchesConversation', 'match_id');
    }

    public function tie_buy()
    {
        return $this->hasMany('App\TieBuy');
    }
}
