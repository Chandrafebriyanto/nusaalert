<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bencana', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('jenis_bencana'); // gempa, tsunami, banjir, cuaca_ekstrem, gunung_api
            $table->decimal('magnitude', 4, 1)->nullable();
            $table->decimal('kedalaman_km', 7, 1)->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('wilayah')->nullable();
            $table->string('sumber_api')->default('bmkg'); // bmkg, owm, komunitas
            $table->json('raw_data')->nullable();
            $table->timestamp('terjadi_pada');
            $table->timestamps();

            $table->index(['jenis_bencana', 'terjadi_pada']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bencana');
    }
};
