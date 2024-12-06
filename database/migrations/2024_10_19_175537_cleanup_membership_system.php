<?php

use App\Enums\MembershipTypeEnum;
use App\Models\Member;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('primary');
            $table->dropColumn('is_pet');
            $table->dropColumn('is_lifelong');
            $table->dropColumn('is_honorary');
            $table->dropColumn('is_donor');
            $table->dropColumn('is_pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('primary')->default(false);
            $table->boolean('is_pet')->default(false);
            $table->boolean('is_lifelong')->default(false);
            $table->boolean('is_honorary')->default(false);
            $table->boolean('is_donor')->default(false);
            $table->boolean('is_pending')->default(false);
        });

        foreach (Member::all() as $member) {
            match ($member->membership_type) {
                MembershipTypeEnum::PENDING => $member->is_pending = true,
                MembershipTypeEnum::PET => $member->is_pet = true,
                MembershipTypeEnum::LIFELONG => $member->is_lifelong = true,
                MembershipTypeEnum::HONORARY => $member->is_honorary = true,
                MembershipTypeEnum::DONOR => $member->is_donor = true,
                MembershipTypeEnum::REGULAR => $member->is_pending = false,
            };
            $member->save();
        }
    }
};
