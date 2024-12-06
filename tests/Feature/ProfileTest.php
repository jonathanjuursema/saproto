<?php

use App\Models\Member;

it('shows the profile page to a user', function () {
    /** @var Member $member */
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get(route('user::profile'));
    $response->assertStatus(200);
    $response->assertSee($member->user->name);
});
