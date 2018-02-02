<?php

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('test')->insert(['test_name' => 'Port Connectivity', 'test_description' => '', 'result_type' => 'PASS/FAIL']);
        DB::table('test')->insert(['test_name' => 'DHCP Server', 'test_description' => '', 'result_type' => 'IP Address']);
        DB::table('test')->insert(['test_name' => 'Routing and Internet connectivity', 'test_description' => '', 'result_type' => 'PASS/FAIL']);
        DB::table('test')->insert(['test_name' => 'DNS Resolution', 'test_description' => '', 'result_type' => 'PASS/FAIL']);
        DB::table('test')->insert(['test_name' => 'Throughput', 'test_description' => '', 'result_type' => 'Megabits per second']);
        
    }
}
