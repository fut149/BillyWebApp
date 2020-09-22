<?php

use Illuminate\Database\Seeder;

class UserGroupsTableSeeder extends Seeder
{
    private $usersGroups = [];

    public function __construct()
    {
        $this->usersGroups[] = [
            'name' => 'default',
            'natureId'=>'expense',
            'priority'=> 0,
            'billy_gorup_id' => '',
            'billy_created_at' => date('Y-m-d H:i:s'),
            'billy_updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Returns the list of generated users
     *
     * @return array
     */
    public function getUserGroups(): array
    {
        return $this->usersGroups;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->usersGroups as $usersGroup) {
            UserGroups::create($usersGroup);
        }
    }
}
