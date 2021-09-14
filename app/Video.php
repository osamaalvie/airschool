<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'url', 'user_id', 'status', 'name'];

    protected $appends = ['thumbnail'];

    public function user()
    {
        return $this->hasOne(User::class, 'id','user_id');
    }

    public function getThumbnailAttribute()
    {
        $path = 'storage/uploads/' . $this->user->id . '/' . $this->name . '.jpg';
        if (file_exists(public_path($path))) {
            return asset($path);
        }
        return '';
    }
}
