<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Intervention\Intervention;
use App\Domain\Intervention\InterventionPriority;
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

    public function toDomain(): Intervention
    {
        return new Intervention(
            $this->id,
            $this->antenna_id,
            $this->description,
            $this->technician_identity,
            InterventionPriority::from($this->priority),
            \DateTimeImmutable::createFromInterface($this->created_at),
            $this->ended_at ? \DateTimeImmutable::createFromInterface($this->ended_at) : null,
        );
    }

    public function antenna(): BelongsTo
    {
        return $this->belongsTo(AntennaModel::class, 'antenna_id');
    }
}
