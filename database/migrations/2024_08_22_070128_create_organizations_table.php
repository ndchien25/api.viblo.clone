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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('organization_users', function (Blueprint $table) {
            $table->bigInteger('organ_id');
            $table->bigInteger('user_id');
            $table->enum('role', ['admin', 'member'])->default('member');
            $table->integer('total_post')->default(0);
            $table->integer('total_member')->default(0);
            $table->bigInteger('total_view')->default(0);
            $table->timestamp('joined_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('organization_users');
    }
};
