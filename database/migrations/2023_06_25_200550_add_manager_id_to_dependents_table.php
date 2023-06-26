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
        Schema::table('dependents', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->after('id');
            $table->foreign('manager_id')->references('id')->on('managers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dependents', function (Blueprint $table) {
            $table->dropColumn('manager_id');
        });
    }
};
