<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Permission;
use Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:syncroles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the roles and permissions needed for the application.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    public function handle(): int
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        Artisan::call('config:cache');

        $this->line('Syncing permissions.');

        /** @var Permission[] $permissions */
        $permissions = [];

        foreach (Permission::all() as $permission) {
            if (! in_array($permission->name, array_keys(Config::array('permission.permissions')))) {
                $permission->delete();
                $this->warn("Removed '$permission->name' permission.");
            }
        }

        foreach (Config::array('permission.permissions') as $name => $permission) {
            $permissions[$name] = Permission::query()->updateOrCreate(
                ['name' => $name],
                [
                    'display_name' => $permission->display_name,
                    'description' => $permission->description,
                    'guard_name' => $permission->guard_name ?? 'web',
                ]
            );
            $this->info("Added/Updated '{$name}' permission.");
        }

        $this->line('Syncing roles.');

        /** @var Role[] $roles */
        $roles = [];

        foreach (Role::all() as $role) {
            if (! in_array($role->name, array_keys(Config::array('permission.roles')))) {
                $role->delete();
                $this->warn("Removed '$role->name' permission.");
            }
        }

        foreach (Config::array('permission.roles') as $name => $role) {
            $roles[$name] = Role::query()->updateOrCreate(
                ['name' => $name],
                [
                    'display_name' => $role->display_name,
                    'description' => $role->description,
                    'guard_name' => $role->guard_name ?? 'web',
                ]
            );
            $this->info("Added/Updated '{$name}' role.");

            $roles[$name]->syncPermissions($role->permissions == '*' ? array_keys($permissions) : $role->permissions);
            $this->info("Synced permissions for '{$name}' role.");
        }

        $this->line('Finished syncing permissions and roles.');

        return 0;
    }
}
