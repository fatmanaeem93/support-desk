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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('raw_text');

            $table->enum('category', ['technical', 'billing', 'account', 'feature_request', 'other'])
                ->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                ->nullable();
            $table->float('category_confidence')->nullable();
            $table->float('priority_confidence')->nullable();

            $table->text('suggested_reply')->nullable();

            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');

            $table->timestamps();

            $table->index(['status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
