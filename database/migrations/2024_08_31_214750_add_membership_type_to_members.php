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
            $table->integer('membership_type')->default(MembershipTypeEnum::PENDING);
        });

        Member::query()->chunk(25, function ($members) {
            foreach ($members as $member) {
                /** @var Member $member */
                if ($member->is_pending) {
                    $member->update([
                        'membership_type' => MembershipTypeEnum::PENDING,
                    ]);

                    continue;
                }

                if ($member->is_pet) {
                    $member->update([
                        'membership_type' => MembershipTypeEnum::PET,
                    ]);

                    continue;
                }

                if ($member->is_lifelong) {
                    $member->update([
                        'membership_type' => MembershipTypeEnum::LIFELONG,
                    ]);

                    continue;
                }

                if ($member->is_honorary) {
                    $member->update([
                        'membership_type' => MembershipTypeEnum::HONORARY,
                    ]);

                    continue;
                }

                if ($member->is_donor) {
                    $member->update([
                        'membership_type' => MembershipTypeEnum::DONOR,
                    ]);

                    continue;
                }

                $member->update([
                    'membership_type' => MembershipTypeEnum::REGULAR,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('membership_type');
        });
    }
};
