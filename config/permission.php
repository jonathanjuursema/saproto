<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [
    'permissions' => [
        'sysadmin' => (object) [
            'display_name' => 'System Admin',
            'description' => 'Gives admin access to the application.',
        ],
        'board' => (object) [
            'display_name' => 'Board Access',
            'description' => 'Gives access to the association administration.',
        ],
        'protube' => (object) [
            'display_name' => 'Protube Admin',
            'description' => 'Gives Protube admin access.',
        ],
        'omnomcom' => (object) [
            'display_name' => 'OmNomCom Access',
            'description' => 'Gives access to the OmNomCom administration.',
        ],
        'finadmin' => (object) [
            'display_name' => 'Financial Admin',
            'description' => 'Gives access to the financial administration.',
        ],
        'tipcie' => (object) [
            'display_name' => 'TIPCie Access',
            'description' => 'Gives access to the TIPCie tools.',
        ],
        'drafters' => (object) [
            'display_name' => 'Guild of Drafters Access',
            'description' => 'Gives access to the relevant tools for drafters.',
        ],
        'alfred' => (object) [
            'display_name' => "Alfred's Workshop",
            'description' => 'Manages OmNomCom for workshop functionality.',
        ],
        'header-image' => (object) [
            'display_name' => 'Update Header Image',
            'description' => "Allows updating the site's header images.",
        ],
        'protography' => (object) [
            'display_name' => 'Photo Access',
            'description' => 'Allows managing photos and albums.',
        ],
        'publishalbums' => (object) [
            'display_name' => 'Publish Albums',
            'description' => 'Allows publishing photo albums.',
        ],
        'registermembers' => (object) [
            'display_name' => 'Register Members',
            'description' => 'Allows finalisation of memberships.',
        ], 'senate' => (object) [
            'display_name' => 'Codex Access',
            'description' => 'Allows managing of codices.',
        ],
        'closeactivities' => (object) [
            'display_name' => 'Activity Closer',
            'description' => 'Allows closing of activities.',
        ],
    ],

    'roles' => [
        'sysadmin' => (object) [
            'display_name' => 'System Administrator',
            'description' => 'Can access all website functionality',
            'permissions' => '*',
        ],
        'admin' => (object) [
            'display_name' => 'Application Administrator',
            'description' => 'Can administrate the website',
            'permissions' => ['board', 'omnomcom', 'finadmin', 'tipcie', 'protube', 'drafters', 'header-image', 'protography', 'publishalbums', 'registermembers', 'closeactivities'],
        ],
        'protube' => (object) [
            'display_name' => 'Protube Administrator',
            'description' => 'Can access Protube admin interface',
            'permissions' => 'protube',
        ],
        'board' => (object) [
            'display_name' => 'Association Board',
            'description' => 'Can administrate the website',
            'permissions' => ['board', 'omnomcom', 'tipcie', 'protube', 'drafters', 'registermembers'],
        ],
        'omnomcom' => (object) [
            'display_name' => 'OmNomCom',
            'description' => 'Can manage the OmNomCom store',
            'permissions' => ['omnomcom'],
        ],
        'finadmin' => (object) [
            'display_name' => 'Financial Administrator',
            'description' => 'Can manage all financials',
            'permissions' => ['finadmin', 'closeactivities'],
        ],
        'tipcie' => (object) [
            'display_name' => 'TipCie',
            'description' => 'Can manage the TipCie store',
            'permissions' => ['tipcie', 'drafters', 'omnomcom'],
        ],
        'drafters' => (object) [
            'display_name' => 'Guild of Drafters',
            'description' => 'Can access the TipCie store',
            'permissions' => 'drafters',
        ],
        'alfred' => (object) [
            'display_name' => 'Alfred',
            'description' => 'This person is Alfred',
            'permissions' => ['alfred', 'omnomcom'],
        ],
        'protography-admin' => (object) [
            'display_name' => 'Protography Administrator',
            'description' => 'Can manage photos and albums',
            'permissions' => ['header-image', 'protography', 'publishalbums'],
        ],
        'protography' => (object) [
            'display_name' => 'Protography',
            'description' => 'Can upload photos',
            'permissions' => 'protography',
        ],
        'registration-helper' => (object) [
            'display_name' => 'Registration Helper',
            'description' => 'Can help register members',
            'permissions' => ['registermembers'],
        ], 'senate' => (object) [
            'display_name' => 'Senate',
            'description' => 'May view and edit codexes',
            'permissions' => ['senate'],
        ],
        'activity-closer' => (object) [
            'display_name' => 'Activity Closer',
            'description' => 'May close activities',
            'permissions' => ['closeactivities'],
        ],
    ],

    'models' => [

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */

        'permission' => Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */

        'role' => Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'permission_role',
    ],

    'column_names' => [

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */

        'model_morph_key' => 'model_id',
    ],

    /*
     * When set to true, the required permission names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_permission_in_exception' => false,

    /*
     * When set to true, the required role names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are disabled.
     */

    'enable_wildcard_permission' => false,

    'cache' => [

        /*
         * By default all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store all permissions.
         */

        'key' => 'spatie.permission.cache',

        /*
         * When checking for a permission against a model by passing a Permission
         * instance to the check, this key determines what attribute on the
         * Permissions model is used to cache against.
         *
         * Ideally, this should match your preferred way of checking permissions, eg:
         * `$user->can('view-posts')` would be 'name'.
         */

        'model_key' => 'name',

        /*
         * You may optionally indicate a specific cache driver to use for permission and
         * role caching using any of the `store` drivers listed in the cache.php config
         * file. Using 'default' here means to use the `default` set in cache.php.
         */

        'store' => 'default',
    ],
];
