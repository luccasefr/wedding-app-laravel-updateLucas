<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Mpociot\Firebase\SyncsWithFirebase;


class Event extends Model
{

    /**
     * @OA\Schema(
     *     schema="Event",
     *     required={"id", "name", "date", "adress_id", "user_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="Cha de Panela"
     *     ),
     *     @OA\Property(
     *         property="date",
     *         type="date",
     *         example="18/03/2018"     
     *     ),
     *     @OA\Property(
     *         property="adress_id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="date",
     *         example=1
     *     )
     * )
    */    
    protected $fillable = ['user_id','address_id','name','date'];
    protected $dates=['created_at','update_at','date'];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y H:i', $value);
    }

    public function getDateAttribute()
    {
        $date=new Carbon($this->attributes['date']);
        return $date->format('d/m/Y H:i');
    }

    public function guests()
    {
        return $this->belongsToMany('App\Guest')->withPivot('confirmed');
    }
    
    public function address()
    {
        return $this->belongsTo('App\Address');
    }
}
