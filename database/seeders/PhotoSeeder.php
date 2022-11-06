<?php

namespace Database\Seeders;

use Carbon;
use Illuminate\Database\Seeder;
use Intervention\Image\Facades\Image;
use Proto\Models\Photo;
use Proto\Models\PhotoAlbum;
use Proto\Models\PhotoLikes;
use Faker\Factory;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (PhotoAlbum::all() as $album){
            $album->delete();
        }
        $faker = Factory::create();

        $n = 12 / 2;
        $time_start = microtime(true);

        foreach (range(1, $n) as $index) {
            $album = PhotoAlbum::create([
                'id'=>$index,
                'name'=>$faker->lastName,
                'date_create'=>Carbon::now()->valueOf(),
                'date_taken'=>Carbon::now()->valueOf(),
                'thumb_id'=>0,
                'event_id'=>null,
                'private'=>mt_rand(1, 4) <= 1,
                'published'=>mt_rand(1, 2) > 1,
            ]);
            echo "\e[33mCreating:\e[0m  ".$index.'/'.$n." albums\r";

            $addWatermark = mt_rand(1, 2) > 1;
            foreach (range(1, $n) as $henk) {
                $photo = new Photo();
                $photo->makePhoto(Image::make('https://loremflickr.com/19200/10800') , 'henk.jpg', Carbon::now()->timestamp, $album->private, $album->id, $album->id, $addWatermark, 'Ysbrand');
                $photo->save();

                $album->thumb_id = $album->items->first()->id;
                $album->save();

                if(mt_rand(1, 2) > 1){
                    $like = new PhotoLikes([
                        'user_id' => 1,
                        'photo_id' => $photo->id,
                    ]);
                    $like->save();
                }
                
                
                echo "\e[33mCreating:\e[0m  ".$henk.'/'.$n." Photos\r";
            }
        }
    }
}
