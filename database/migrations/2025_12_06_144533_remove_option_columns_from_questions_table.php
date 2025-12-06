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
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'option_a')) {
                $table->dropColumn('option_a');
            }
            if (Schema::hasColumn('questions', 'option_b')) {
                $table->dropColumn('option_b');
            }
            if (Schema::hasColumn('questions', 'option_c')) {
                $table->dropColumn('option_c');
            }
            if (Schema::hasColumn('questions', 'option_d')) {
                $table->dropColumn('option_d');
            }
            if (Schema::hasColumn('questions', 'correct_option')) {
                $table->dropColumn('correct_option');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('option_a')->nullable();
            $table->string('option_b')->nullable();
            $table->string('option_c')->nullable();
            $table->string('option_d')->nullable();
            $table->string('correct_option')->nullable();
        }); 
        
    }
};
