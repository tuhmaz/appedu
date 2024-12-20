<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->text('page_url')->change();
        });
    }

    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('page_url')->change();
        });
    }
};
