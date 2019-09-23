<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $guarded = [];

    public function gallerable()
    {
        return $this->morphTo();
    }

    public function images()
    {
        return $this->morphMany('App\Image', 'imageable');
    }
}
