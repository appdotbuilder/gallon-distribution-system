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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->unique()->comment('Employee identification number');
            $table->string('name')->comment('Employee full name');
            $table->enum('grade', ['G7', 'G8', 'G9', 'G10', 'G11', 'G12', 'G13'])->comment('Employee grade level');
            $table->integer('monthly_quota')->comment('Monthly gallon quota based on grade');
            $table->boolean('is_active')->default(true)->comment('Employee active status');
            $table->timestamps();
            
            // Indexes for performance
            $table->index('employee_id');
            $table->index('grade');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};