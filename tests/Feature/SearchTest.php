<?php

use App\Models\Committee;
use App\Models\Event;
use App\Models\Page;
use App\Models\Photo;

it('shows the opensearch page', function () {
    $response = $this->get('/opensearch');
    $response->assertSee('S.A. Proto');
    $response->assertStatus(200);
});

it('shows the search page', function () {
    $response = $this->get('/search');
    $response->assertSee('Your search');
    $response->assertStatus(200);
});

it('shows an event on the search page', function () {
    Event::factory()->create([
        'title' => 'TestEvent',
        'secret' => false,
    ]);
    $response = $this->get('/search?query=Test');
    $response->assertSee('TestEvent');
    $response->assertStatus(200);
});

it('shows a photoalbum on the search page', function () {
    /** @var Photo $photo */
    $photo = Photo::factory()->create();

    $photo->album()->withoutGlobalScopes()->update([
        'name' => 'TestAlbum',
        'thumb_id' => $photo->id,
        'private' => false,
        'published' => true,
    ]);

    $response = $this->get('/search?query=Test');
    $response->assertSee('TestAlbum');
    $response->assertStatus(200);
});

it('shows a committee on the search page', function () {
    Committee::factory()->create([
        'name' => 'TestCommittee',
        'public' => true,
        'is_society' => false,
        'is_active' => true,
    ]);

    $response = $this->get('/search?query=Test');
    $response->assertSee('TestCommittee');
    $response->assertStatus(200);
});

it('shows a page on the search page', function () {
    Page::factory()->create([
        'title' => 'TestPage',
        'content' => 'TestContent',
        'is_member_only' => false,
    ]);

    $response = $this->get('/search?query=Test');
    $response->assertSee('TestPage');
    $response->assertStatus(200);
});
