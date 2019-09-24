<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class Post extends Model
{
    protected $guarded = [];
    protected $with = ['cover'];
    protected $appends = ['type', 'url', 'subpageUrl'];

    protected static function boot() {
        parent::boot();

        static::creating(function ($question) {
            $question->slug = Str::slug($question->title, '-');
        });

        static::updating(function ($question) {
            $question->slug = Str::slug($question->title, '-');
        });

        static::saving(function ($question) {
            $question->slug = Str::slug($question->title, '-');
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function subpage()
    {
        return $this->belongsTo('App\Subpage');
    }

    public function cover()
    {
        return $this->morphOne('App\Image', 'imageable');
    }

    public function setCover(Request $request)
    {
        if($request->has('removeCover')) {
            Storage::disk('public')->delete($this->cover->url);
            Image::destroy($this->cover->id);
            $this->save();
        }
        if($request->has('coverFile') && $request->has('coverUrl')) {
            throw ValidationException::withMessages([
                'coverFile' => 'can\'t exists with coverUrl',
                'coverUrl' => 'can\'t exists with coverFile'
            ]);
        }
        else if($request->has('coverFile')) {
            $file = $request->file('coverFile');
            $url = $file->store('covers', 'public');
            return $this->cover()->updateOrCreate(['url' => $url]);
        }
        else if ($request->has('coverUrl')) {
            $file_url = $request->get('coverUrl');
            $header = get_headers($file_url, 1);
            $content_type = $header['Content-Type'];
            if(!($content_type === 'image/jpeg' || $content_type === 'image/png' || $content_type === 'image/gif')) {
                throw ValidationException::withMessages(['error']);
                //throw ValidationException::withMessages(['coverUrl' => 'Not image. Given content-type: ' . $content_type]);
            }
            $file = file_get_contents($file_url);
            if($file === false || strlen($file) < 1){
                throw ValidationException::withMessages(['coverUrl' => 'Failed to download file at: ' . $file_url]);
            }
            switch ($content_type) {
                case 'image/jpeg':
                    $ext = 'jpg';
                    break;
                case 'image/png':
                    $ext = 'png';
                    break;
                case 'image/gif':
                    $ext = 'gif';
                    break;
                default:
                    $ext = 'jpg';
                    break;
            }
            $path = base_path('storage\\app\\public\\download.'.$ext);
            file_put_contents($path, $file);
            $uploaded_file = new UploadedFile($path, 'download.'.$ext);
            $url = $uploaded_file->store('covers', 'public');
            return $this->cover()->updateOrCreate(['url' => $url]);
        }
        return null;
    }

    public function getTypeAttribute()
    {
        return get_class($this);
    }

    public function getUrlAttribute()
    {
        $type = strtolower(substr($this->getTypeAttribute(),4, 1));
        return '/'.$this->subpage->slug.'/'.$type.'/'.$this->slug;
    }

    public function getSubpageUrlAttribute()
    {
        return '/'.$this->subpage->slug;
    }
}
