<?php

namespace App;

use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Mpociot\Firebase\SyncsWithFirebase;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SyncsWithFirebase;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /**
     * @OA\Schema(
     *     schema="User",
     *     required={"id", "name_1", "name_2", "email", "password", "waiting_guests", "quiz_released", "puzzle_released", "memory_game_released", "publications_should_be_aproved"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name_1",
     *         type="string",
     *         example="João"
     *     ),
     *     @OA\Property(
     *         property="name_2",
     *         type="string",
     *         example="Maria"     
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string",
     *         example="joao@gmail.com"
     *     ),
     *     @OA\Property(
     *         property="password",
     *         type="string",
     *         example="*********"
     *     ),
     *     @OA\Property(
     *         property="wedding_date",
     *         type="date",
     *         example="30/12/2018"
     *              
     *     ),
     *     @OA\Property(
     *         property="want_to_spent",
     *         type="integer",
     *         example=50000
     *     ),
     *     @OA\Property(
     *         property="wating_guests",
     *         type="integer",
     *         example=300
     *     ),
     *     @OA\Property(
     *         property="wedding_adress_id",
     *         type="integer",
     *         example="Endereço x, rua y, etc..."
     *              
     *     ),
     *     @OA\Property(
     *         property="quiz_released",
     *         type="tinyint",
     *         example=1
     *              
     *     ),
     *     @OA\Property(
     *         property="puzzle_released",
     *         type="tinyint",
     *         example=1
     *              
     *     ),
     *     @OA\Property(
     *         property="memory_game_released",
     *         type="tinyint",
     *         example=1
     *              
     *     ),
     *     @OA\Property(
     *         property="publications_should_be_aproved",
     *         type="tinyint",
     *         example=1
     *              
     *     ),
     *     @OA\Property(
     *         property="remember_token",
     *         type="string",
     *         example="hhsuioiue0982103909oiwueoqu0198203qu"
     *              
     *     ),
     *     
     * )
    */    
    protected $fillable = [
        'name_1','name_2','wedding_date','want_to_spent','waiting_guests','wedding_address_id','quiz_released','puzzle_released','memory_game_released','publications_should_be_aproved','email','password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates=['created_at','update_at','wedding_date'];

    protected $appends = ['expenses_total'];

    public function guests()
    {
        return $this->hasMany('App\Guest')->with('events');
    }

    public function actions()
    {
        return $this->hasMany('App\Action');
    }

    public function sensible_words()
    {
        return $this->hasMany('App\SensibleWord');
    }

    public function events()
    {
        return $this->hasMany('App\Event')->with('guests','address');
    }

    public function invite()
    {
        return $this->hasOne('App\Invite');
    }

    public function songs()
    {
        return $this->hasMany('App\Song')->with('guests_likes','guest');
    }

    public function puzzle_images()
    {
        return $this->hasMany('App\PuzzleImage');
    }

    public function memory_images()
    {
        return $this->hasMany('App\MemoryGameImage');
    }

    public function chroma_images()
    {
        return $this->hasMany('App\ChromaImage');
    }

    public function address()
    {
        return $this->belongsTo('App\Address','wedding_address_id');
    }

    public function posts()
    {
        $posts;
        foreach ($this->guests as $guest) {
            $post = $guest->posts;
            if(isset($posts)){
                $posts = $posts->concat($post);
            }else {
                $posts = $post;
            }
        }
        return isset($posts) ? $posts:collect([]);
    }

    public function ValidText($text)
    {
        $words = $this->sensible_words;
        foreach ($words as $word) {
            if (strrpos(strtolower($text), strtolower($word->word))!==false) {
                return false;
            }
        }
        return true;
    }

    public function gift_lists()
    {
        return $this->hasMany('App\GiftList');
    }

    public function getExpensesTotalAttribute()
    {
        $expenseValue=0;
        foreach ($this->actions as $action) {
            if($action->expense){
                $expenseValue+=$action->expense_value;
            }
        }

        return $expenseValue;
    }

    public function getInitials()
    {
        return strtoupper($this->name_1[0].$this->name_2[0]);
    }

    public function setWeddingDateAttribute($value)
    {
        $this->attributes['wedding_date'] = Carbon::createFromFormat('d/m/Y H:i', $value);
    }

    public function getWeddingDateAttribute()
    {
        $date=new Carbon($this->attributes['wedding_date']);
        return $date->format('d/m/Y H:i');
    }
}
