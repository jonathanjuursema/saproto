<?php

use App\Models\HeaderImage;
use App\Models\Member;
use App\Models\StorageEntry;
use Illuminate\Http\UploadedFile;

it('lists the headerimages', function () {
    /** @var Member $member */
    $member = Member::factory()->create();
    $member->user->givePermissionTo('header-image');
    $response = $this->actingAs($member->user)
        ->get('/headerimages');
    $response->assertSee('Header Images');
    $response->assertStatus(200);
});

it('does not lists the headerimages for someone without permission', function () {
    /** @var Member $member */
    $member = Member::factory()->create();
    $response = $this->actingAs($member->user)
        ->get('/headerimages');
    $response->assertSee('You are not allowed to access this page');
});

it('lets the appropriate user create a new headerimage', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->givePermissionTo('header-image');
    $response = $this->actingAs($member->user)
        ->get('/headerimages/create');
    $response->assertSee('new header image');
    $response->assertStatus(200);

    $image = HeaderImage::factory()->raw();

    $response = $this->actingAs($member->user)
        ->post('/headerimages', [...$image, 'user' => $image['credit_id'], 'image' => UploadedFile::fake()->image($image['title'].'.jpg')]);
    $response->assertRedirect('/headerimages');
    $response->assertStatus(302);
    $this->assertDatabaseHas('headerimages', [
        'title' => $image['title'],
        'credit_id' => $image['credit_id'],
    ]);
    $this->assertDatabaseHas('files', [
        'original_filename' => $image['title'].'.jpg',
    ]);
});

it('lets the appropriate user delete a headerimage and ensures it does not have the StorageEntry associated anymore', function () {
    /** @var Member $member * */
    $member = Member::factory()->create();
    $member->user->givePermissionTo('header-image');

    /** @var StorageEntry $storageEntry */
    $storageEntry = StorageEntry::factory()->create();
    /** @var HeaderImage $oldImage */
    $oldImage = HeaderImage::factory()->create(
        ['image_id' => $storageEntry->id]
    );

    $response = $this->actingAs($member->user)
        ->delete('/headerimages/'.$oldImage->id);
    $response->assertRedirect('/headerimages/');
    $response->assertStatus(302);
    $this->assertDatabaseMissing('headerimages',
        ['id' => $oldImage->id]
    );

    $this->assertDatabaseMissing('files', ['id' => $storageEntry->id]);
});
