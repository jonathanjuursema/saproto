<?php

use App\Models\HashMapItem;
use App\Models\Newsitem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('newsitems', function (Blueprint $table) {
            $table->boolean('is_weekly')->default(false);
        });

        $lastSent = HashMapItem::query()->where('key', 'newsletter_last_sent')->first();
        $text = HashMapItem::query()->where('key', 'newsletter_text')->first();
        if ($text) {
            $newsItem = new Newsitem([
                'title' => 'Weekly newsletter of week '.Carbon::now()->weekOfYear.' of '.Carbon::now()->year,
                'content' => $text->value,
                'is_weekly' => true,
                'publication' => date('Y-m-d H:i:s', $lastSent->value),
            ]);

            $newsItem->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsitems', function (Blueprint $table) {
            $table->dropColumn('is_weekly');
        });
    }
};
