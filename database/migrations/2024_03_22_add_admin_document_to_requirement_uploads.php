<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('requirement_uploads', function (Blueprint $table) {
            $table->string('admin_document_path')->nullable()->after('file_path');
        });
    }
    public function down()
    {
        Schema::table('requirement_uploads', function (Blueprint $table) {
            $table->dropColumn('admin_document_path');
        });
    }
}; 