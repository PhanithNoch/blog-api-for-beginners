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
            $table->id();  // Add an auto-incrementing primary key column named 'id'

            $table->string('title');
            $table->string('image')->nullable();
            $table->bigInteger('user_id')->unsigned(); // Add a foreign key column for the user who created the post
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        

            $table->timestamps(); // Add timestamps for created_at and updated_at
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
