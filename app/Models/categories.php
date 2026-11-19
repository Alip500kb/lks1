<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class categories extends Model
{
use Notifiable;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'title',
        'description',
        'address',
        'image'
    ];
}
