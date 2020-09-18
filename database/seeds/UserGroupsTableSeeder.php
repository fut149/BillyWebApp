<?php

use Illuminate\Database\Seeder;

class UserGroupsTableSeeder extends Seeder
{
    private $usersGroups = [];

    public function __construct()
    {
        $this->usersGroups[] = [
            'name' => 'group1',
            'natureId'=>'expense',
            'priority'=> 0,
        ];

        $this->usersGroups[] = [
            'name' => 'group2',
            'natureId'=>'expense',
            'priority'=> 0,
        ];
    }

    /**
     * Returns the list of generated users
     *
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
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
