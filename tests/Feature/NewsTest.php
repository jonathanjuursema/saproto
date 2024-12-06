<?php

use App\Models\Member;
use App\Models\Newsitem;

it('shows members the news section on the homepage', function () {
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/');

    $response->assertSee('View older news');
    $response->assertSee('cucumber time');
    $response->assertStatus(200);
});

it('shows members an empty news page', function () {
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/news/index');

    $response->assertSee('no News Articles');
    $response->assertStatus(200);
});

it('lets admins create news', function ($article) {
    $member = Member::factory()->create();
    $member->user->assignRole('board');
    $response = $this->actingAs($member->user)
        ->get('/news/create');

    $response->assertSee('Create a new');
    $response->assertStatus(200);

    $response = $this->actingAs($member->user)
        ->post('/news/store', $article);

    $this->assertDatabaseHas('newsitems', [
        'title' => $article['title'] ?? 'Weekly update for week '.date('W').' of '.date('Y').'.',
        'content' => $article['content'],
        'is_weekly' => $article['is_weekly'],
    ]);

    $id = Newsitem::query()->where('content', $article['content'])->first()->id;

    $response->assertRedirect('/news/edit/'.$id);

})->with([
    'newsitem' => fn (): array => array_merge(Newsitem::factory()->raw(), ['title' => fake()->sentence()]),
    'weekly' => fn () => Newsitem::factory()->isWeekly()->raw(),
]);

it('does not let non board members create news', function () {
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/news/create');
    $response->assertStatus(200);

    $response->assertDontSee('Create a new');
    $response->assertSee('You are not allowed to access this page');

    $article = Newsitem::factory()->raw();
    $response = $this->actingAs($member->user)
        ->post('/news/store', $article);

    $response->assertStatus(200);
    $response->assertSee('You are not allowed to access this page');
});
