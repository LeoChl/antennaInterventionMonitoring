<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterventionModel extends Model
{
    public $timestamps = false;

    protected $table = 'antenna_intervention';

    protected $fillable = [
        'antenna_id',
        'description',
        'technician_identity',
        'priority',
        'created_at',
        'ended_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function antenna(): BelongsTo
    {
        return $this->belongsTo(AntennaModel::class, 'antenna_id');
    }
}
