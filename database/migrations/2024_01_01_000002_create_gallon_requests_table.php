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
        Schema::create('gallon_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->comment('Number of gallons requested');
            $table->enum('status', ['pending', 'approved', 'ready', 'completed'])->default('pending')->comment('Request status');
            $table->timestamp('requested_at')->comment('When the request was made');
            $table->timestamp('approved_at')->nullable()->comment('When admin approved the request');
            $table->timestamp('ready_at')->nullable()->comment('When warehouse marked as ready');
            $table->timestamp('completed_at')->nullable()->comment('When employee picked up gallons');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['employee_id', 'status']);
            $table->index('requested_at');
            $table->index('status');
            $table->index(['requested_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallon_requests');
    }
};