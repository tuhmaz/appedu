<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('session_id')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->string('user_agent');
            $table->string('page_url');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device_type')->nullable();
            $table->integer('response_time')->nullable();
            $table->timestamps();

            $table->index(['ip_address', 'created_at']);
            $table->index(['session_id', 'last_activity']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitors');
    }
};
