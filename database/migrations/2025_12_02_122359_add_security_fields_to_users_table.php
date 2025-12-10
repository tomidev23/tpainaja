<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxx_add_security_fields_to_users_table.php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable()->after('email');
        $table->timestamp('phone_verified_at')->nullable()->after('phone');
        $table->boolean('two_factor_enabled')->default(false)->after('phone_verified_at');
        $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['phone', 'phone_verified_at', 'two_factor_enabled', 'two_factor_secret']);
    });
}
};
