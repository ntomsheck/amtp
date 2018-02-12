<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name', 20);
            $table->string('description');
            $table->string('details');
            $table->string('result_type');
            $table->string('minimum_threshold')->default('');
            $table->string('maximum_threshold')->default('');
            $table->string('order');
            $table->tinyInteger('continue_port_on_failure')->default(1);
            $table->tinyInteger('continue_unit_on_failure')->default(1);
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
        Schema::dropIfExists('test');
    }
}
