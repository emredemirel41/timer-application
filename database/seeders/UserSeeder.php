<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 
         User::truncate();

         //Create Super User
         User::factory()->create([
             'name' => 'Berkay Altuğ',
             'email' => 'berkayaltug1@gmail.com',
             'is_master' => true,
         ]);

         //Create Normal User
         User::factory(10)->create();
         DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
