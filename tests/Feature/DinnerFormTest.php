<?php

use App\Models\Dinnerform;
use App\Models\DinnerformOrderline;
use App\Models\Member;
use App\Models\User;

it('shows a public Dinnerform on the homepage to members but not to users', function () {
    Dinnerform::factory()->create([
        'restaurant' => 'TestDinnerform',
        'closed' => false,
        'visible_home_page' => true,
        'end' => now()->addDay(),
    ]);

    /** @var Member $member */
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/');
    $response->assertSee('TestDinnerform');
    $response->assertStatus(200);

    /** @var User $user */
    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->get('/');
    $response->assertDontSee('TestDinnerform');
    $response->assertStatus(200);
});

it('lets user order on a dinnerform', function () {
    $this->withoutExceptionHandling();
    /** @var Dinnerform $dinnerform */
    $dinnerform = Dinnerform::factory()->create([
        'restaurant' => 'TestDinnerform',
        'closed' => false,
        'visible_home_page' => false,
        'start' => now()->subHour(),
        'end' => now()->addDay(),
    ]);

    /** @var Member $member */
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get(route('dinnerform::show', ['id' => $dinnerform->id]));

    $response->assertSee('Add an order');
    $response->assertStatus(200);

    $response = $this->actingAs($member->user)
        ->post('/dinnerform/orderline/store/'.$dinnerform->id, [
            'order' => 'TestOrder',
            'price' => 1.0,
            'helper' => false,
        ]);

    $response->assertRedirect('/dinnerform/'.$dinnerform->id);
    $response->assertStatus(302);

    $this->assertDatabaseHas('dinnerform_orderline', [
        'user_id' => $member->user->id,
        'dinnerform_id' => $dinnerform->id,
        'description' => 'TestOrder',
        'price' => 1,
        'helper' => 1,
        'closed' => 0,
    ]);

    $response = $this->actingAs($member->user)
        ->get('/dinnerform/'.$dinnerform->id);

    $response->assertSee('TestOrder');
});

it('lets users delete their order on a dinnerform', function () {
    /** @var DinnerformOrderline $orderline */
    $orderline = DinnerformOrderline::factory()->create();

    $response = $this->actingAs($orderline->user)
        ->get('/dinnerform/orderline/delete/'.$orderline->id);

    $response->assertRedirect('/');
    $response->assertStatus(302);

    $response->assertSessionHas('flash_message', 'Your order has been deleted!');

    $this->assertDatabaseMissing('dinnerform_orderline', [
        'description' => $orderline->description,
    ]);
});
