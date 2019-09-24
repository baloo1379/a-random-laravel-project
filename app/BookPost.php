<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

/**
 * App\BookPost
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $author
 * @property string|null $city
 * @property string|null $year
 * @property string|null $pdf
 * @property int $subpage_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Image $cover
 * @property-read mixed $subpage_url
 * @property-read mixed $type
 * @property-read mixed $url
 * @property-read \App\Subpage $subpage
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost wherePdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereSubpageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\BookPost whereYear($value)
 * @mixin \Eloquent
 */
class BookPost extends Post
{
    public function setPdf(Request $request)
    {
        if($request->has('removePdf')) {
            $this->pdf = null;
            $this->save();
        }
        if($request->has('pdf')) {
            $file = $request->file('pdf');
            $url = $file->store('pdfs', 'public');
            $this->pdf = $url;
            $this->save();
            return $url;
        }
        return null;
    }
}
