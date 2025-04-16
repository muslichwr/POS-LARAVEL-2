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
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->change();
        });
    }
    
    public function down()
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->nullable()->default(null)->change();
        });
    }
};
