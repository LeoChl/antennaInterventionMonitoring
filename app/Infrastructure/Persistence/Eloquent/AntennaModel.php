<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AntennaModel extends Model
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

    public function latestIntervention(): HasOne
    {
        return $this->hasOne(InterventionModel::class, 'antenna_id')->latestOfMany('created_at');
    }

    public function activeIntervention(): HasOne
    {
        return $this->hasOne(InterventionModel::class, 'antenna_id')->whereNull('ended_at');
    }
}
