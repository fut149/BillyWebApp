<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use UserGroupsSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_groups')->insert([
                                       'id' => 1,
                                       'name' => 'default',
                                       'natureId'=>'expense',
                                       'priority'=> 0,
                                       'billy_gorup_id' => 'hVliBHqcTLGCLj5f927kzw',
                                       'billy_created_at' => date('Y-m-d H:i:s'),
                                       'billy_updated_at' => date('Y-m-d H:i:s'),
                                       'created_at' => date('Y-m-d H:i:s'),
                                       'updated_at' => date('Y-m-d H:i:s'),
                                   ]);
    }
}
