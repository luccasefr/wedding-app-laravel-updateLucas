<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Mpociot\Firebase\SyncsWithFirebase;

class Action extends Model
{

    use SyncsWithFirebase;

    /**
     * @OA\Schema(
     *     schema="Action",
     *     required={"id", "title", "expense", "notify_guests", "user_id"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="title",
     *         type="string",
     *         example="Cha de Panela"
     *     ),
     *     @OA\Property(
     *         property="expense",
     *         type="tinyint",
     *         example=1     
     *     ),
     *     @OA\Property(
     *         property="expense_value",
     *         type="double",
     *         example=850.06
     *     ),
     *     @OA\Property(
     *         property="expense_date",
     *         type="date",
     *         example="17/01/2018"
     *     ),
     *     @OA\Property(
     *         property="notify_guests",
     *         type="tinyint",
     *         example=1
     *              
     *     ),
     *     @OA\Property(
     *         property="notify_date_from",
     *         type="date",
     *         example="17/01/2018"
     *     ),
     *     @OA\Property(
     *         property="notify_date_to",
     *         type="date",
     *         example="17/02/2018"
     *     ),
     *     @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Compareça"
     *              
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         example=1
     *              
     *     )
     * )
    */    
    protected $fillable=['title','expense','expense_value','expense_date','notify_guests','notify_date_from','notify_date_to','message','user_id'];
    protected $dates=['created_at','update_at','expense_date','notify_date'];

    public function setExpenseDateAttribute($value)
    {
        $this->attributes['expense_date'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function isBetweenNotifyDate()
    {
        $now = Carbon::now();
        $notify_dat_from=new Carbon($this->attributes['notify_date_from']);
        $notify_dat_to=new Carbon($this->attributes['notify_date_to']);
        return $notify_dat_from->lessThanOrEqualTo($now) && $notify_dat_to->greaterThanOrEqualTo($now);
    }

    public function setNotifyDateFromAttribute($value)
    {
        $this->attributes['notify_date_from'] = Carbon::createFromFormat('d/m/Y', $value);
    }
    public function setNotifyDateToAttribute($value)
    {
        $this->attributes['notify_date_to'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    public function getExpenseDateAttribute()
    {
        $date=new Carbon($this->attributes['expense_date']);
        return $date->format('d/m/Y');
    }

    public function getNotifyDateFromAttribute()
    {
        $date=new Carbon($this->attributes['notify_date_from']);
        return $date->format('d/m/Y');
    }

    public function getNotifyDateToAttribute()
    {
        $date=new Carbon($this->attributes['notify_date_to']);
        return $date->format('d/m/Y');
    }
}
