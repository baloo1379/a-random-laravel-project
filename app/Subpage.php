<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

/**
 * App\Subpage
 *
 * @method static create($attr)
 * @method static update($attr)
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookPost[] $books
 * @property-read int|null $books_count
 * @property-read mixed $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\NewsPost[] $newses
 * @property-read int|null $newses_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Subpage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subpage extends Model
{
    protected $guarded = [];
    protected $appends = ['posts'];
    protected $post_types = ['newses', 'books'];

    protected static function boot() {
        parent::boot();

        static::creating(function ($question) {
            $question->slug = Str::slug($question->name, '-');
        });

        static::updating(function ($question) {
            $question->slug = Str::slug($question->name, '-');
        });

        static::saving(function ($question) {
            $question->slug = Str::slug($question->name, '-');
        });
    }

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
