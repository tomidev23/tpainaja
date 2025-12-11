<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('exams', function (Blueprint $table) {
        $table->boolean('is_completed')->default(0)->after('logo');
    });
}

public function down()
{
    Schema::table('exams', function (Blueprint $table) {
        $table->dropColumn('is_completed');
    });
}

};
