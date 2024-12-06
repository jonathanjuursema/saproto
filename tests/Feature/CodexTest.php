<?php

use App\Models\Codex;
use App\Models\CodexSong;
use App\Models\CodexText;
use App\Models\Member;

it('lists the codices for the senate', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->assignRole('senate');
    $response = $this->actingAs($member->user)
        ->get('/codex');
    $response->assertSee('Codices');
    $response->assertStatus(200);
});

it('does not lists the codices for non senate', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/codex');
    $response->assertSee('You are not allowed to access this page');
});

it('lets the senate create a new codex', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->assignRole('senate');
    $response = $this->actingAs($member->user)
        ->get('/codex/create');
    $response->assertSee('Codex details');
    $response->assertStatus(200);

    $newCodex = Codex::factory()->raw();
    $response = $this->actingAs($member->user)
        ->post('/codex', $newCodex);
    $response->assertRedirect('/codex');
    $response->assertStatus(302);
    $this->assertDatabaseHas('codex_codices', [
        'name' => $newCodex['name'],
        'export' => $newCodex['export'],
        'description' => $newCodex['description'],
    ]);
});

it('lets the senate update a codex and assign songs and texts', function () {
    /** @var Codex $oldCodex */
    $oldCodex = Codex::factory()->create();
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->assignRole('senate');

    $response = $this->actingAs($member->user)
        ->get('/codex/'.$oldCodex->id.'/edit');
    $response->assertSee('Codex details');
    $response->assertSee($oldCodex->name);
    $response->assertStatus(200);

    $songs = CodexSong::factory()->count(3)->create();
    $texts = CodexText::factory()->count(3)->create();
    $response = $this->actingAs($member->user)
        ->patch('/codex/'.$oldCodex->id, [...$oldCodex->toArray(), 'name' => 'New Name', 'songids' => $songs->pluck('id')->toArray(), 'textids' => $texts->pluck('id')->toArray()]);

    $response->assertRedirect('/codex/');
    $response->assertStatus(302);
    $this->assertDatabaseHas('codex_codices', [
        'name' => 'New Name',
        'export' => $oldCodex->export,
        'description' => $oldCodex->description,
        'id' => $oldCodex->id,
    ]);

    $oldCodex->refresh();
    $this->assertEquals($songs->pluck('id')->toArray(), $oldCodex->songs->pluck('id')->toArray());
    $this->assertEquals($texts->pluck('id')->toArray(), $oldCodex->texts->pluck('id')->toArray());
});

it('lets the senate delete a codex and ensures it does not have the texts associated anymore', function () {
    /** @var Codex $oldCodex * */
    $oldCodex = Codex::factory()->create();
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->assignRole('senate');

    $oldCodex->songs()->attach(CodexSong::factory()->create());
    $oldCodex->texts()->attach(CodexText::factory()->create());

    $response = $this->actingAs($member->user)
        ->delete('/codex/'.$oldCodex->id);
    $response->assertRedirect('/codex/');
    $response->assertStatus(302);
    $this->assertDatabaseMissing('codex_codices',
        ['name' => $oldCodex->name,
            'export' => $oldCodex->export,
            'description' => $oldCodex->description,
            'id' => $oldCodex->id,
        ]
    );

    $this->assertDatabaseMissing('codex_codex_song', ['codex' => $oldCodex->id]);
    $this->assertDatabaseMissing('codex_codex_text', ['codex' => $oldCodex->id]);
});
