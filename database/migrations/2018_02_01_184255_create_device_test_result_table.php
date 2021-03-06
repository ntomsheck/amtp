<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeviceTestResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_test_result', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_test_id')->unsigned();
            $table->char('interface', 20);
            $table->char('test_name', 20);
            $table->text('result');
            //$table->tinyInteger('pass');
            $table->timestamps();
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_test_result');
    }
}
