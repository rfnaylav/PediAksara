<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('librarian_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->date('loan_date');
            $table->date('due_date')->index();
            $table->unsignedTinyInteger('extend_count')->default(0);
            $table->string('status')->default('dipinjam')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
