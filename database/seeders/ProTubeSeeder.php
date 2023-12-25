<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Artisan;
use DB;

class ProTubeSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($output)
    {
        $output->info('Seeding data for ProTube');

        # Create the oauth client
        $output->task("Creating ProTube OAUTH client", function () { 
            Artisan::call("passport:client", [
                "--name" => "ProTube",
                "--no-interaction" => true,
                "--redirect_uri" => "http://localhost:3000/auth/login/callback"
            ]);
        });

        $client = DB::table("oauth_clients")
            ->where("name", "ProTube")
            ->latest()
            ->first();
        
        DB::table("oauth_clients")
            ->where("id", $client->id)
            ->update([
                'password_client' => true
            ]);
    
        $output->info("<options=bold>ProTube OAUTH Id:</> <fg=green>$client->id</>
               <options=bold>secret:</> <fg=green>$client->secret</>");

        // Creating 2 users, one admin, one non-admin
        $users = [
            'admin' => [
                'admin' => true,
                'email' => 'admin@protube.nl',
                'password' => 'ADMIN',
                'calling_name' => 'protube_admin'
            ],
            'user' => [
                'admin' => false,
                'email' => 'user@protube.nl',
                'password' => 'USER',
                'calling_name' => 'protube_user'
            ]
        ];

        foreach ($users as $name => $u) {
            $user = User::factory()
                ->state([
                    'email' => $u['email'],
                    'calling_name' => $u['calling_name']
                ])
                ->create();
            
            $user->setPassword($u['password']);        
            
            Member::create([
                'user_id' => $user->id,
                'proto_username' => 'dev.protube_' . $name
            ]);
            
            if ($u['admin']) {
                $user->assignRole('protube');
            }
        }
    }
}
