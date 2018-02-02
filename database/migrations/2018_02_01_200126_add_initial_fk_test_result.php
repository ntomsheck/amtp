<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInitialFkTestResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_test_result', function (Blueprint $table) {
            $table->foreign('device_test_id')
                    ->references('id')->on('device_test')
                    ->onDelete('cascade');
            
            $table->foreign('test_id')
                    ->references('id')->on('test');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_test_result', function (Blueprint $table) {
            //
        });
    }
}
