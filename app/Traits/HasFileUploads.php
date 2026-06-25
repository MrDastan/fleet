<?php

namespace App\Traits;

use App\Models\FileUpload;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFileUploads
{
    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'uploadable');
    }
}
