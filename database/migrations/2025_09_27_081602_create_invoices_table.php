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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained()->onDelete('cascade');
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal_invoice');
            $table->decimal('total_harga', 15, 2);
            $table->enum('status', ['draft', 'issued', 'paid'])->default('draft');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
