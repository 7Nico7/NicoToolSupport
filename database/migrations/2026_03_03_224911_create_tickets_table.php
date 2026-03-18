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
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');

            $table->foreignId('status_id')->constrained('ticket_statuses');
            $table->foreignId('priority_id')->constrained('ticket_priorities');
            $table->foreignId('type_id')->constrained('ticket_types');
            $table->foreignId('category_id')->constrained('ticket_categories');

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('helpdesk_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('due_date')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
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
