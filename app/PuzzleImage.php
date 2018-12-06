<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PuzzleImage extends Model
{
    protected $fillable = ['img_url','user_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
