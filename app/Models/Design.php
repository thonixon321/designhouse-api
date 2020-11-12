<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentTaggable\Taggable;

class Design extends Model
{
    use Taggable;
    //fields (cols) we want in the DB for this model
    protected $fillable = [
        'user_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_successful',
        'disk'
    ];

    public function user()
    {
        //each design belongs to a user
        return $this->belongsTo(User::class);
    }

    //getter/mutator to access the $this->images - even though we don't have that column in the DB (we have an image column); this is utilized in the DesignResource
    public function getImagesAttribute()
    {
      
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'large' => $this->getImagePath('large'),
            'original' => $this->getImagePath('original'),
        ];
    }

    protected function getImagePath($size)
    {
          //using $this->disk because it could be either local or s3 and the url() class within the storage class will take where this is stored on the disk and our folder structure (for instance amazon s3) and generate the url based on that -
          //for example, if the disk is set to s3 this will return something like: https://design-house.s3.us-west-2.amazonaws.com/uploads/designs/thumbnail/your-image.jpg and if it were set to public: https://design-house.test/storage/uploads/designs/thumbnail/your-image.jpg
        return Storage::disk($this->disk)
                         ->url("uploads/designs/{$size}/".$this->image);
    }
}
