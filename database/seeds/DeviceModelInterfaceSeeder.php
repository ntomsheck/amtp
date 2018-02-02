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
//        DB::table('device_model_interface')->insert(['model_id' => '1', 'interface_number' => '1', 'interface_name' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '1', 'interface_number' => '2', 'interface_name' => 'LAN2']);
//        
//        //6500-A2/3G
//        DB::table('device_model_interface')->insert(['model_id' => '2', 'interface_number' => '1', 'interface_name' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '2', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        
        //6500-E
        DB::table('device_model_interface')->insert(['model_id' => '3', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '3', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        
        //6500-E/3G
        DB::table('device_model_interface')->insert(['model_id' => '4', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '4', 'interface_number' => '2', 'interface_name' => 'LAN2']);

//        //6500W-A2
//        DB::table('device_model_interface')->insert(['model_id' => '5', 'interface_number' => '1', 'interface_name' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '5', 'interface_number' => '2', 'interface_name' => 'LAN2']);
//
//        //6500W-A2/3G
//        DB::table('device_model_interface')->insert(['model_id' => '6', 'interface_number' => '1', 'interface_name' => 'LAN1']);
//        DB::table('device_model_interface')->insert(['model_id' => '6', 'interface_number' => '2', 'interface_name' => 'LAN2']);        
        
        //6500-M
        DB::table('device_model_interface')->insert(['model_id' => '7', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'interface_number' => '3', 'interface_name' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'interface_number' => '4', 'interface_name' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '7', 'interface_number' => '5', 'interface_name' => 'WIFI']);
        
        //6500-M/LTE
        DB::table('device_model_interface')->insert(['model_id' => '8', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'interface_number' => '3', 'interface_name' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'interface_number' => '4', 'interface_name' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '8', 'interface_number' => '5', 'interface_name' => 'WIFI']);

        //6500-M1/LTE
        DB::table('device_model_interface')->insert(['model_id' => '9', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'interface_number' => '3', 'interface_name' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'interface_number' => '4', 'interface_name' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '9', 'interface_number' => '5', 'interface_name' => 'WIFI']);

        //6500-MW1
        DB::table('device_model_interface')->insert(['model_id' => '10', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'interface_number' => '3', 'interface_name' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'interface_number' => '4', 'interface_name' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '10', 'interface_number' => '5', 'interface_name' => 'WIFI']);

        //6500-MW1/3G
        DB::table('device_model_interface')->insert(['model_id' => '11', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'interface_number' => '3', 'interface_name' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'interface_number' => '4', 'interface_name' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '11', 'interface_number' => '5', 'interface_name' => 'WIFI']);

        //6500-MW1/LTE
        DB::table('device_model_interface')->insert(['model_id' => '12', 'interface_number' => '1', 'interface_name' => 'LAN1']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'interface_number' => '2', 'interface_name' => 'LAN2']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'interface_number' => '3', 'interface_name' => 'LAN3']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'interface_number' => '4', 'interface_name' => 'LAN4']);
        DB::table('device_model_interface')->insert(['model_id' => '12', 'interface_number' => '5', 'interface_name' => 'WIFI']);     
    }
}
