<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class AntennaModel extends Model
{
    public $timestamps = false;

    protected $table = 'antenna';

    protected $fillable = [
        'name',
        'city',
        'status',
        'created_at',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
    ];
}
