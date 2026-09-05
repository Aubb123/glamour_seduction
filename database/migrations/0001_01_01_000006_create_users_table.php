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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID pour une identification universelle unique
            //
            $table->foreignId('country_id')->constrained('geo_countries')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers la table geo_countries
            //
            $table->string('first_name'); // Prénom de l'utilisateur
            $table->string('last_name'); // Nom de famille de l'utilisateur
            $table->string('slug')->unique(); // Slug lisible (first_name + last_name)
            $table->string('email')->unique(); // Adresse email unique
            // Son genre
            $table->enum('gender', ['male', 'female']); // Genre de l'utilisateur (Ex: masculin, féminin)
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone', 30)->nullable(); // Numéro de téléphone de l'utilisateur
            $table->timestamp('phone_verified_at')->nullable(); // Date de vérification du téléphone
            $table->string('password');
            $table->enum('status', ['active', 'suspended', 'banned'])->default('suspended'); // Statut de l'utilisateur (Ex: actif, suspendu, banni)
            //
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['country_id', 'status', 'deleted_at']); // Index pour les filtres courants
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            // Token unique pour la réinitialisation du mot de passe
            $table->string('token')->unique();
            $table->string('otp_code')->unique();
            $table->dateTime('reset_token_expires_at');
            // Informations supplémentaires pour la réinitialisation du mot de passe
            $table->string('email')->nullable(); // Adresse e-mail de l'utilisateur
            $table->string('phone')->nullable(); // Numéro de téléphone de l'utilisateur
            $table->dateTime('used_at')->nullable(); // Date d'utilisation du token
            $table->dateTime('expired_at')->nullable(); // Date d'expiration du token
            $table->timestamps();
            // Index pour les filtres courants
            $table->index(['user_id', 'reset_token_expires_at']); // Index pour les filtres courants
            $table->index(['email']); // Recherche par email (utilisateurs non connectés)
        });

        Schema::create('account_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            // Token unique pour la réinitialisation du compte
            $table->string('token')->unique();
            $table->string('otp_code')->unique();
            $table->dateTime('reset_token_expires_at');
            // Informations supplémentaires pour la réinitialisation du compte
            $table->string('email')->nullable(); // Adresse e-mail de l'utilisateur
            $table->string('phone')->nullable(); // Numéro de téléphone de l'utilisateur
            $table->dateTime('used_at')->nullable(); // Date d'utilisation du token
            $table->dateTime('expired_at')->nullable(); // Date d'expiration du token
            $table->timestamps();
            // Index pour les filtres courants
            $table->index(['user_id', 'reset_token_expires_at']); // Index pour les filtres courants
            $table->index(['email']); // Recherche par email (utilisateurs non connectés)
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('account_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
