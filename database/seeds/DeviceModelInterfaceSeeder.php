<?php

use Illuminate\Database\Seeder;

class DeviceModelInterfaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        //6500-A2
//        DB::table('device_model_interface')->insert(['model_id' => '1', 'index' => '1', 'description' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '1', 'index' => '2', 'description' => 'LAN2']);
//        
//        //6500-A2/3G
//        DB::table('device_model_interface')->insert(['model_id' => '2', 'index' => '1', 'description' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '2', 'index' => '2', 'description' => 'LAN2']);
        
        //6500-E
        DB::table('device_model_interface')->insert(['model_id' => '3', 'name' => 'lan1', 'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '3', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);
        
        //6500-E/3G
        DB::table('device_model_interface')->insert(['model_id' => '4', 'name' => 'lan1',  'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '4', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);

//        //6500W-A2
//        DB::table('device_model_interface')->insert(['model_id' => '5', 'index' => '1', 'description' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '5', 'index' => '2', 'description' => 'LAN2']);
//
//        //6500W-A2/3G
//        DB::table('device_model_interface')->insert(['model_id' => '6', 'index' => '1', 'description' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '6', 'index' => '2', 'description' => 'LAN2']);        
        
        //6500-M
        DB::table('device_model_interface')->insert(['model_id' => '7', 'name' => 'lan1',  'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'name' => 'lan3',  'index' => '3', 'description' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'name' => 'lan4',  'index' => '4', 'description' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'name' => 'wifi',  'index' => '5', 'description' => 'WIFI']);
        
        //6500-M/LTE
        DB::table('device_model_interface')->insert(['model_id' => '8', 'name' => 'lan1',  'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'name' => 'lan3',  'index' => '3', 'description' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'name' => 'lan4',  'index' => '4', 'description' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'name' => 'wifi',  'index' => '5', 'description' => 'WIFI']);

        //6500-M1/LTE
        DB::table('device_model_interface')->insert(['model_id' => '9', 'name' => 'lan1',  'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'name' => 'lan3',  'index' => '3', 'description' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'name' => 'lan4',  'index' => '4', 'description' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'name' => 'wifi',  'index' => '5', 'description' => 'WIFI']);

        //6500-MW1
        DB::table('device_model_interface')->insert(['model_id' => '10', 'name' => 'lan1',  'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'name' => 'lan3',  'index' => '3', 'description' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'name' => 'lan4',  'index' => '4', 'description' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'name' => 'wifi',  'index' => '5', 'description' => 'WIFI']);

        //6500-MW1/3G
        DB::table('device_model_interface')->insert(['model_id' => '11', 'name' => 'lan1',  'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'name' => 'lan3',  'index' => '3', 'description' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'name' => 'lan4',  'index' => '4', 'description' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'name' => 'wifi',  'index' => '5', 'description' => 'WIFI']);

        //6500-MW1/LTE
        DB::table('device_model_interface')->insert(['model_id' => '12', 'name' => 'lan1',  'index' => '1', 'description' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'name' => 'lan2',  'index' => '2', 'description' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'name' => 'lan3',  'index' => '3', 'description' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'name' => 'lan4',  'index' => '4', 'description' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'name' => 'wifi',  'index' => '5', 'description' => 'WIFI']);     
    }
}
