<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('requirement_uploads', function (Blueprint $table) {
            $table->string('payment_path')->nullable();
            $table->string('payment_status')->nullable();
            $table->text('payment_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('requirement_uploads', function (Blueprint $table) {
            $table->dropColumn('payment_path');
            $table->dropColumn('payment_status');
            $table->dropColumn('payment_note');
        });
    }
}; 