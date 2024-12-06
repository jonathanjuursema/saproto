<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameProtomailToProtousername extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function ($table) {
            $table->text('proto_mail')->nullable(false)->change();
            $table->renameColumn('proto_mail', 'proto_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function ($table) {
            $table->text('proto_username')->nullable(true)->change();
            $table->renameColumn('proto_username', 'proto_mail');
        });
    }
}
