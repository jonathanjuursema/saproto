<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\PhotoLikes;
use Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Intervention\Image\Facades\Image;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (PhotoAlbum::all() as $album) {
            $album->delete();
        }
        $faker = Factory::create();

        $n = 12 / 2;

        foreach (range(1, $n) as $index) {
            $album = PhotoAlbum::create([
                'id' => $index,
                'name' => $faker->lastName,
                'date_create' => Carbon::now()->valueOf(),
                'date_taken' => Carbon::now()->valueOf(),
                'thumb_id' => 0,
                'event_id' => null,
                'private' => mt_rand(1, 4) <= 1,
                'published' => mt_rand(1, 2) > 1,
            ]);
            echo "\e[33mCreating:\e[0m  ".$index.'/'.$n." albums\r";

            $addWatermark = mt_rand(1, 2) > 1;
            foreach (range(1, $n) as $henk) {
                $photo = new Photo();
                try {
                    $photo->makePhoto(Image::make('https://loremflickr.com/1920/1080'), 'henk.jpg', Carbon::now()->timestamp, $album->private, $album->id, $album->id, $addWatermark, 'Ysbrand');
                    $photo->save();

                    $album->thumb_id = $album->items->first()->id;
                    $album->save();

                    if (mt_rand(1, 2) > 1) {
                        $like = new PhotoLikes([
                            'user_id' => 1,
                            'photo_id' => $photo->id,
                        ]);
                        $like->save();
                    }
                } catch (\Exception $e) {
                }

                echo "\e[33mCreating:\e[0m  ".$henk.'/'.$n." Photos\r";
            }
        }
    }
}
