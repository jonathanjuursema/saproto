<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dmx_channels', function ($table) {
            $table->integer('id')->unsigned()->primary()->change();
        });

        $this->dropForeign();

        $defaultConnection = config('database.default');
        $databaseName = config("database.connections.{$defaultConnection}.database");

        // Get the list of all tables
        $tableNames = DB::table('information_schema.tables')
            ->where('table_schema', $databaseName)
            ->where('table_type', '=', 'BASE TABLE')
            ->get(['TABLE_NAME'])
            ->pluck('TABLE_NAME');

        // Get the list of all columns in the active db that have a collation
        $columns = DB::table('information_schema.columns')
            ->where('table_schema', $databaseName)
            ->whereIn('table_name', $tableNames)
            ->where(function ($query) {
                $query->where('COLUMN_TYPE', 'int(10) unsigned')
                    ->orWhere('COLUMN_TYPE', 'int(11)');
            })->whereNotNull('COLUMN_KEY')
            ->get();

        // Iterate through the list and alter each column
        foreach ($columns as $column) {
            $tableName = $column->TABLE_NAME;
            $columnName = $column->COLUMN_NAME;
            $increment = $columnName === 'id' ? ' AUTO_INCREMENT' : '';

            DB::unprepared("ALTER TABLE `{$tableName}` MODIFY `{$columnName}` BIGINT UNSIGNED{$increment};");
        }

        // Recreate the foreign keys
        $this->reinstateForeign();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the 'id' column change in the 'dmx_channels' table
        Schema::table('dmx_channels', function ($table) {
            $table->integer('id')->unsigned(false)->primary()->change();  // Revert to original type
        });

        // Drop foreign keys
        $this->dropForeign();

        // Revert column types from BIGINT UNSIGNED to original types (typically int or int unsigned)
        $defaultConnection = config('database.default');
        $databaseName = config("database.connections.{$defaultConnection}.database");

        // Get the list of all tables
        $tableNames = DB::table('information_schema.tables')
            ->where('table_schema', $databaseName)
            ->where('table_type', '=', 'BASE TABLE')
            ->get(['TABLE_NAME'])
            ->pluck('TABLE_NAME');

        // Get the list of all columns in the active db that were changed to BIGINT UNSIGNED
        $columns = DB::table('information_schema.columns')
            ->where('table_schema', $databaseName)
            ->whereIn('table_name', $tableNames)
            ->where(function ($query) {
                $query->where('COLUMN_TYPE', 'bigint(20) unsigned');
            })
            ->get();

        // Iterate through the list and alter each column back to int(10) unsigned or int(11)
        foreach ($columns as $column) {
            $tableName = $column->TABLE_NAME;
            $columnName = $column->COLUMN_NAME;

            // Alter column type back to unsigned int (you may need to adjust depending on the original type)
            $increment = $columnName === 'id' ? ' AUTO_INCREMENT' : '';

            DB::unprepared("ALTER TABLE `{$tableName}` MODIFY `{$columnName}` INT(10) UNSIGNED{$increment};");
        }

        $this->reinstateForeign();
    }

    public function dropForeign(): void
    {
        Schema::table('role_user', function ($table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('model_has_permissions', function ($table) {
            $table->dropForeign(['permission_id']);
        });

        Schema::table('permission_role', function ($table) {
            $table->dropForeign(['permission_id']);
            $table->dropForeign(['role_id']);
        });

        Schema::table('model_has_roles', function ($table) {
            $table->dropForeign(['role_id']);
        });

        Schema::table('pages_files', function ($table) {
            $table->dropForeign(['file_id']);
            $table->dropForeign(['page_id']);
        });
    }

    public function reinstateForeign(): void
    {
        Schema::table('role_user', function ($table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('model_has_permissions', function ($table) {
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::table('permission_role', function ($table) {
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::table('model_has_roles', function ($table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::table('pages_files', function ($table) {
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }
};
