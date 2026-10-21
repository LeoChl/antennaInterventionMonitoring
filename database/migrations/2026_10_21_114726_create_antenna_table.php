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
        Schema::create('antenna', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('city');
            $table->enum('status', ['UP', 'DOWN'])->default('UP');
            $table->timestampTz('created_at')->useCurrent();
            $table->unique('name', 'uq_antenna_name');
            $table->index(['city', 'status'], 'idx_antenna_city_status');
        });

        DB::statement("ALTER TABLE antenna ADD CONSTRAINT chk_antenna_name_not_blank CHECK (btrim(name) <> '')");
        DB::statement("ALTER TABLE antenna ADD CONSTRAINT chk_antenna_city_not_blank CHECK (btrim(city) <> '')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antenna');
    }
};
