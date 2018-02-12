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
        DB::table('test')->insert(['name' => 'connectivity', 'description' => 'Port Connectivity', 'details' => '', 'result_type' => 'PASS/FAIL', 'order' => 1, 'continue_port_on_failure' => false]);
        DB::table('test')->insert(['name' => 'dhcp', 'description' => 'DHCP Server', 'details' => '', 'result_type' => 'IP Address', 'order' => 2, 'continue_port_on_failure' => false]);
        DB::table('test')->insert(['name' => 'routing', 'description' => 'Routing and Internet connectivity', 'details' => '', 'result_type' => 'PASS/FAIL', 'order' => 3]);
        DB::table('test')->insert(['name' => 'dns', 'description' => 'DNS Resolution', 'details' => '', 'result_type' => 'PASS/FAIL', 'order' => 4]);
        DB::table('test')->insert(['name' => 'throughput', 'description' => 'Throughput', 'details' => '', 'result_type' => 'Megabits per second', 'order' => 5]);
        
    }
}
