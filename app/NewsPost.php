<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class NewsPost extends Post
{
    public function galleries()
    {
        return $this->morphMany('App\Gallery', 'gallerable');
    }
}
