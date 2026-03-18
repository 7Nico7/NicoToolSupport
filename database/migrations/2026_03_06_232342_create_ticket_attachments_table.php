<?php
// database/migrations/2025_01_01_000001_create_ticket_attachments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete(); // el archivo desaparece si se elimina el ticket

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // el archivo sobrevive si se elimina el usuario

            // ── Archivo ──────────────────────────────────────────────────────
            $table->string('filename');           // nombre original: "captura-error.png"
            $table->string('path');               // ruta interna: "tickets/5/abc123.png"
            $table->string('disk')->default('local'); // 'local' | 's3'
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');   // bytes

            // ── Contexto ─────────────────────────────────────────────────────
            // Explica qué muestra el archivo — no existe en message_attachments
            // porque ahí el mensaje ya provee el contexto.
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
