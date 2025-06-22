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
        Schema::create('history_gaji_kloters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kloter_id')->nullable()->constrained('kloters')->onDelete('cascade');
            $table->string('kode')->unique()->nullable();
            $table->integer('jml_karyawan');
            $table->double('total_gaji');
            $table->timestamp('waktu');
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_gaji_kloter');
    }
};
