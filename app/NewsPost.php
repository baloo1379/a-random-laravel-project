<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * App\NewsPost
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $body
 * @property int $subpage_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Image $cover
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read mixed $subpage_url
 * @property-read mixed $type
 * @property-read mixed $url
 * @property-read \App\Subpage $subpage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost whereSubpageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NewsPost whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NewsPost extends Post
{
    public function galleries()
    {
        return $this->morphMany('App\Gallery', 'gallerable');
    }
}
