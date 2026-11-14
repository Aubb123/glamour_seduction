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
        Schema::create('cloud_files', function (Blueprint $table) {
            $table->id(); // Clé primaire auto-incrémentée
            $table->uuid('uuid')->unique(); // UUID public unique du fichier
            //
            $table->foreignId('cloud_folder_id')->constrained('cloud_folders')->cascadeOnUpdate()->cascadeOnDelete(); // Clé étrangère vers cloud_folders
            //
            $table->string('fileable_type'); // Type de modèle associé (ex: App\Models\Post, App\Models\User, etc.)
            $table->unsignedBigInteger('fileable_id'); // ID du modèle associé
            //
            $table->string('cloud_file_id')->nullable(); // Identifiant du fichier chez le fournisseur cloud (réservé, toujours null : le chemin cloud est stocké dans remote_path)
            $table->string('remote_path')->nullable(); // Chemin du fichier sur le stockage cloud (B2). local_path = chemin local, remote_path = chemin cloud.
            $table->string('file_name'); // Nom du fichier
            $table->enum('file_type', ['video', 'image', 'zip', 'others']); // Type de fichier
            $table->unsignedBigInteger('file_size')->nullable(); // Taille du fichier
            $table->string('mime_type'); // Type MIME du fichier Ex: image/jpeg, video/mp4, application/zip, etc.
            $table->text('shareable_link')->nullable(); // Lien partageable du fichier
            $table->string('hash')->nullable(); // Hash du fichier, volontairement toujours null (déduplication possible via l'index composite cloud_files_folder_hash_unique), nullable pour les fichiers non encore synchronisés
            $table->json('metadata')->nullable(); // Métadonnées supplémentaires pour le fichier
            //
            $table->enum('sync_status', ['pending', 'processing', 'synced', 'error'])->default('pending'); // Statut de synchronisation cloud : En attente, en cours, synchronisé, erreur. Un fichier inséré est en attente (pending) tant qu'il n'est pas migré.
            $table->string('local_path')->nullable(); // Chemin du fichier en stockage local avant migration (vide une fois migré)
            $table->timestamp('last_sync_attempt_at')->nullable(); // Date de la dernière tentative de migration (permet de gérer les retries)
            //
            $table->timestamps();
            $table->softDeletes();
            // NB : pas d'index explicite sur cloud_folder_id : la contrainte de clé
            // étrangère foreignId()->constrained() crée déjà un index sur cette colonne.
            $table->index('file_type'); // Index pour les requêtes basées sur le type de fichier
            $table->index('sync_status');
            $table->index(['fileable_type', 'fileable_id']); // Index pour les relations polymorphiques
            // Unicité composite (cloud_folder_id, hash) : un même motif est possible dans
            // deux dossiers différents. En MySQL, l'index unique autorise les NULL multiples,
            // donc le hash reste nullable tant qu'il n'est pas calculé.
            $table->unique(['cloud_folder_id', 'hash'], 'cloud_files_folder_hash_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_files');
    }
};
