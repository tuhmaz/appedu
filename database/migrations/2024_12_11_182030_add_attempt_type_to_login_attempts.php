<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttemptTypeToLoginAttempts extends Migration
{
    public function up()
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            // إضافة عمود نوع المحاولة فقط إذا لم يكن موجوداً
            if (!Schema::hasColumn('login_attempts', 'attempt_type')) {
                $table->string('attempt_type')->nullable()->after('successful');
            }
        });
    }

    public function down()
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropColumn('attempt_type');
        });
    }
}
