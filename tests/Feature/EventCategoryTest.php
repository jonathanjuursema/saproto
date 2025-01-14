<?php

use App\Models\EventCategory;
use App\Models\Member;

it('lists the events categories', function () {
    /** @var Member $member */
    $member = Member::factory()->create();
    $member->user->assignRole('board');
    $response = $this->actingAs($member->user)
        ->get('/events/categories/create');
    $response->assertSee('new category');
    $response->assertStatus(200);
});

it('does not lists the event categories for someone without permission', function () {
    /** @var Member $member */
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/events/categories/create');
    $response->assertSee('You are not allowed to access this page');
});

it('lets the board create a new event category', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->assignRole('board');

    $event = EventCategory::factory()->raw();

    $response = $this->actingAs($member->user)
        ->post('/events/categories', $event);
    $response->assertStatus(302);

    $this->assertDatabaseHas('event_categories', [
        'name' => $event['name'],
        'icon' => $event['icon'],
    ]);
});

it('lets the board delete an event category', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->assignRole('board');

    /** @var EventCategory $event */
    $event = EventCategory::factory()->create();

    $response = $this->actingAs($member->user)
        ->delete('/events/categories/'.$event->id);
    $response->assertRedirect('/events/categories/create');
    $response->assertStatus(302);
    $this->assertDatabaseMissing('event_categories',
        ['id' => $event->id]
    );
});
