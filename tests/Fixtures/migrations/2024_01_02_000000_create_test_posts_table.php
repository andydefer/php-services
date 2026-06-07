<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('body');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('test_users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_posts');
    }
};
