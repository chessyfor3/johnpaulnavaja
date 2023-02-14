<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Role;
use App\Models\User;
use App\Models\UserFacility;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_count = 5;
        $facility_count = 5;
        $user_count = 5;
        User::factory($user_count)->create();
        Role::factory($role_count)->create();
        Facility::factory($facility_count)->create();

        $users = User::all();

        foreach($users as $user) {
            $role = rand(1, $user_count);
            for ($i=1; $i <= $role ; $i++) { 
                UserRole::create([
                    'user_id' => $user->id,
                    'role_id' => $i,
                ]);
            }

            $facility = rand(1, $facility_count);
            for ($x=1; $x <= $facility ; $x++) { 
                UserFacility::create([
                    'user_id' => $user->id,
                    'facility_id' => $x,
                ]);
            }
        }

    }
}
