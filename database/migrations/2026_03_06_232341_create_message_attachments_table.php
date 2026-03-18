<?php
// database/migrations/2025_01_01_000002_create_message_attachments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_message_id')
                ->constrained('ticket_messages')
                ->cascadeOnDelete(); // el archivo desaparece si se elimina el mensaje

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // el archivo sobrevive si se elimina el usuario

            // ── Archivo ──────────────────────────────────────────────────────
            $table->string('filename');
            $table->string('path');
            $table->string('disk')->default('local');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');

            // SIN campo description — el texto del mensaje ya explica el archivo.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};
