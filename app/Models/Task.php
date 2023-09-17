<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use function array_slice;
use function gettype;
use function json_decode;
use function json_encode;
use function serialize;
use function set;
use function unserialize;

class Task extends Model
{

    protected $fillable = [
        'name',
        'progress',
        'job_completed',
        'job_started',
    ];

    protected $casts = [
        'output' => 'array'
    ];


    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            $model->output = [];
        });
    }
}
