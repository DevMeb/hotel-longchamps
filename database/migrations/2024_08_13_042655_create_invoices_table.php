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
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade'); // Clé étrangère vers la table reservations
            $table->string('subject'); // Objet de la facture
            $table->text('description'); // Désignation ou détails supplémentaires
            $table->date('billing_start_date'); // Date de début de la période de facturation
            $table->date('billing_end_date'); // Date de fin de la période de facturation
            $table->timestamp('issued_at')->nullable(); // Date d'émission de la facture
            $table->timestamp('paid_at')->nullable(); // Date de paiement de la facture
            $table->enum('status', ['pending', 'issued', 'paid'])->default('pending'); // Statut de la facture
            $table->timestamps(); // created_at et updated_at
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
