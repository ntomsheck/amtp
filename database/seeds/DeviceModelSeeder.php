<?php

use Illuminate\Database\Seeder;

class DeviceModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('device_model')->insert(['id' => '1', 'model_name' => '6500-A2', 'number_of_interfaces' => '2']);
//        DB::table('device_model')->insert(['id' => '2', 'model_name' => '6500-A2/3G', 'number_of_interfaces' => '2']);
        DB::table('device_model')->insert(['id' => '3', 'model_name' => '6500-E']);
        DB::table('device_model')->insert(['id' => '4', 'model_name' => '6500-E/3G']);
//        DB::table('device_model')->insert(['id' => '5', 'model_name' => '6500W-A2', 'number_of_interfaces' => '3']);
//        DB::table('device_model')->insert(['id' => '6', 'model_name' => '6500W-A2/3G', 'number_of_interfaces' => '3']);        
        DB::table('device_model')->insert(['id' => '7', 'model_name' => '6500-M']);
        DB::table('device_model')->insert(['id' => '8', 'model_name' => '6500-M/LTE']);
        DB::table('device_model')->insert(['id' => '9', 'model_name' => '6500-M1/LTE']);
        DB::table('device_model')->insert(['id' => '10', 'model_name' => '6500-MW1']);
        DB::table('device_model')->insert(['id' => '11', 'model_name' => '6500-MW1/3G']);
        DB::table('device_model')->insert(['id' => '12', 'model_name' => '6500-MW1/LTE']);
    }
}
