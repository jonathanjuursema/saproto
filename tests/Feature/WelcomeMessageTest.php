<?php

use App\Models\Member;
use App\Models\WelcomeMessage;

it('lists the welcomeMessages for board', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->givePermissionTo('board');
    $response = $this->actingAs($member->user)
        ->get('/welcomeMessages');
    $response->assertSee('Welcome Messages');
    $response->assertStatus(200);
});

it('does not lists the welcomeMessages for non board', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/welcomeMessages');
    $response->assertSee('You are not allowed to access this page.');
});

it('lets the senate create a new welcomeMessage', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->givePermissionTo('board');

    $newWelcomeMessage = WelcomeMessage::factory()->raw();
    $response = $this->actingAs($member->user)
        ->post('/welcomeMessages', $newWelcomeMessage);
    $response->assertRedirect('/welcomeMessages');
    $response->assertStatus(302);
    $this->assertDatabaseHas('user_welcome', [
        'user_id' => $newWelcomeMessage['user_id'],
        'message' => $newWelcomeMessage['message'],
    ]);
});

it('lets the senate update a welcomeMessage', function () {
    /** @var WelcomeMessage $oldWelcomeMessage */
    $oldWelcomeMessage = WelcomeMessage::factory()->create();

    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->givePermissionTo('board');

    $response = $this->actingAs($member->user)
        ->get('/welcomeMessages/');

    $response->assertSee($oldWelcomeMessage->message);
    $response->assertStatus(200);

    $response = $this->actingAs($member->user)
        ->post('/welcomeMessages', [
            'user_id' => $oldWelcomeMessage->user_id,
            'message' => 'New Message',
        ]);

    $response->assertRedirect('/welcomeMessages');
    $response->assertStatus(302);
    $this->assertDatabaseHas('user_welcome', [
        'user_id' => $oldWelcomeMessage['user_id'],
        'message' => 'New Message',
    ]);

    $response = $this->actingAs($member->user)
        ->get('/welcomeMessages/');
    $response->assertSee('New Message');
    $response->assertStatus(200);
});

it('lets the board delete a WelcomeMessage', function () {
    /** @var WelcomeMessage $oldWelcomeMessage */
    $oldWelcomeMessage = WelcomeMessage::factory()->create();
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->givePermissionTo('board');

    $response = $this->actingAs($member->user)
        ->delete('/welcomeMessages/'.$oldWelcomeMessage->id);
    $response->assertRedirect('/welcomeMessages/');
    $response->assertStatus(302);
    $response->assertDontSee($oldWelcomeMessage->message);
    $this->assertDatabaseMissing('user_welcome',
        [
            'user_id' => $oldWelcomeMessage['user_id'],
            'message' => $oldWelcomeMessage['message'],
        ]
    );
});
