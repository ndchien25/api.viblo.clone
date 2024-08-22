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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('serie_id')->nullable();
            $table->bigInteger('organ_id')->nullable();
            $table->string('title');
            $table->mediumText('content');
            $table->string('slug')->unique();
            $table->enum('status', ['private_draft', 'anyone_with_link', 'schedule', 'public'])->default('private_draft');
            $table->timestamp('schedule_at')->nullable();
            $table->timestamp('publish_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('vote')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
