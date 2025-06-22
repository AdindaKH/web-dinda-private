<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ton_ikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kloter_id')->nullable()->constrained('kloters')->onDelete('set null');
            $table->decimal('jumlah_ton', 8, 2);
            $table->bigInteger('harga_ikan_per_ton')->default(1000000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ton_ikans');
    }
};
