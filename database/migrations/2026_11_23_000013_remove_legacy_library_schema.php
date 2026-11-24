<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pengembalians', 'peminjamans', 'anggotas', 'bukus'] as $table) {
            if (Schema::hasTable($table) && DB::table($table)->exists()) {
                throw new \RuntimeException(
                    "Legacy table [$table] contains data. Export or migrate that data before applying the PRD schema migration."
                );
            }
        }

        Schema::dropIfExists('pengembalians');
        Schema::dropIfExists('peminjamans');
        Schema::dropIfExists('anggotas');
        Schema::dropIfExists('bukus');
    }

    public function down(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku')->unique();
            $table->string('isbn')->nullable()->unique();
            $table->string('judul');
            $table->string('penulis')->nullable();
            $table->string('penerbit')->nullable();
            $table->unsignedSmallInteger('tahun_terbit')->nullable();
            $table->unsignedInteger('stok')->default(0);
            $table->timestamps();
        });

        Schema::create('anggotas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_anggota')->unique();
            $table->string('nama');
            $table->string('email')->nullable()->unique();
            $table->string('nomor_telepon', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->restrictOnDelete();
            $table->foreignId('buku_id')->constrained('bukus')->restrictOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo')->index();
            $table->timestamps();
        });

        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->unique()->constrained('peminjamans')->restrictOnDelete();
            $table->date('tanggal_kembali');
            $table->unsignedInteger('denda')->default(0);
            $table->timestamps();
        });
    }
};
