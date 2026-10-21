<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('antenna_intervention', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('antenna_id');
            $table->string('description');
            $table->string('technician_identity');
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH']);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('ended_at')->nullable();

            $table->foreign('antenna_id', 'fk_intervention_antenna')
                ->references('id')
                ->on('antenna')
                ->restrictOnDelete();
        });

        DB::statement('ALTER TABLE antenna_intervention ADD CONSTRAINT chk_intervention_ended_after_created CHECK (ended_at IS NULL OR ended_at >= created_at)');
        DB::statement('CREATE INDEX idx_intervention_antenna_created ON antenna_intervention (antenna_id, created_at DESC)');
        DB::statement('CREATE UNIQUE INDEX uq_one_active_intervention_per_antenna ON antenna_intervention (antenna_id) WHERE ended_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antenna_intervention');
    }
};
