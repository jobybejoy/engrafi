<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(['name' => 'Student','description' => str_random(10)]);
        DB::table('roles')->insert(['name' => 'Faculty','description' => str_random(10)]);
        // DB::table('roles')->insert(['name' => 'Event Coordinator','description' => str_random(10)]);
        // DB::table('roles')->insert(['name' => 'Program Coordinator','description' => str_random(10)]);    
        // DB::table('roles')->insert(['name' => 'HOD','description' => str_random(10)]); 
        // DB::table('roles')->insert(['name' => 'DEAN','description' => str_random(10)]);
        DB::table('roles')->insert(['name' => 'Admin','description' => str_random(10)]);    
    }     
}
