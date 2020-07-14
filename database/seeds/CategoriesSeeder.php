<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'name' => 'deportes',            
        ]);//

        DB::table('categories')->insert([
            'name' => 'videojuegos',            
        ]);
        DB::table('categories')->insert([
            'name' => 'autos',            
        ]);

        DB::table('categories')->insert([
            'name' => 'cine',  
            
        ]);
        
        DB::table('categories')->insert([
            'name' => 'desarrollo web',            
        ]);

    }
}
