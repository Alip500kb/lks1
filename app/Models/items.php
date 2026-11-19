<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class items extends Model
{
    use Notifiable;

    protected $fillable = [
        'id',
        'name',
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'address',
        'city',
        'phone',
        'image',
        'status',
    ];
    public $timestamps = false;
}
