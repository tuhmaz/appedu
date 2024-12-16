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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('commentable_id'); // ID of the related model
            $table->string('commentable_type'); // Model class name
            $table->string('connection_name'); // اسم قاعدة البيانات (jo, sa, ps, etc.)
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index for better performance
            $table->index(['commentable_id', 'commentable_type', 'connection_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
