<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'teste',
            'email' => 'teste@teste.com',
            'password' => bcrypt('admin'),
        ]);
    }
}
