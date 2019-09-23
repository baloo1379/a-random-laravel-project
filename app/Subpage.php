<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @method static create($attr)
 * @method static update($attr)
 */
class Subpage extends Model
{
    protected $guarded = [];
    protected $appends = ['posts'];
    protected $post_types = ['newses', 'books'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function newses()
    {
        return $this->hasMany('App\NewsPost');
    }

    public function books()
    {
        return $this->hasMany('App\BookPost');
    }

    public function posts()
    {
        $posts = array();
        foreach ($this->post_types as $post_type) {
            if(method_exists($this, $post_type)) {
                array_push($posts, $this->$post_type()->get());
            }
        }
        return collect($posts)->collapse();
    }

    public function findPost($slug)
    {
        foreach ($this->post_types as $post_type) {
            if(method_exists($this, $post_type)) {
                if($this->$post_type()->where('slug', $slug)->get()->count() > 0) {
                    return $this->$post_type()->where('slug', $slug);
                }
            }
        }
        return null;
    }

    public function findPostOrFail($slug)
    {
        if(is_null($this->findPost($slug))) throw new ModelNotFoundException("No query results for model [App\\Post] $slug");
        else return $this->findPost($slug);
    }

    public function getPostsAttribute()
    {
        $posts = array();
        foreach ($this->post_types as $post_type) {
            if(method_exists($this, $post_type)) {
                array_push($posts, $this->$post_type()->pluck('slug'));
            }
        }
        return collect($posts)->collapse();
    }
}
