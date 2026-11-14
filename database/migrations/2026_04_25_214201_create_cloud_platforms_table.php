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
        Schema::create('cloud_platforms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            //
            $table->enum('name', ['backblaze_b2']); // aws,gdrive,dropbox,azure,google_cloud,ibm_cloud,oracle_cloud,digitalocean,linode,vultr,backblaze_b2,wasabi,upcloud,scaleway,hetzner,cloudflare_r2 alibaba_cloud,rackspace,openstack,owncloud,nextcloud
            $table->enum('disk_name', ['b2']); // backblaze_b2,s3,local,wasabi,upcloud,scaleway,hetzner,cloudflare_r2 alibaba_cloud,rackspace,openstack,owncloud,nextcloud
            $table->string('label')->unique();
            $table->string('slug')->unique();
            $table->string('api_endpoint');
            $table->string('api_key')->unique()->nullable();
            $table->string('api_secret')->unique()->nullable();
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            //
            $table->timestamps();
            $table->softDeletes();
            // Index pour les filtres courants
            $table->index(['status', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_platforms');
    }
};
