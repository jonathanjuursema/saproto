<?php

use App\Models\Feedback;
use App\Models\FeedbackCategory;
use App\Models\FeedbackVote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFeedbackFunctionality extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback_votes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('feedback_id');
            $table->integer('vote');
            $table->timestamps();
        });

        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->bigInteger('feedback_category_id')->default(1);
            $table->text('feedback');
            $table->boolean('reviewed')->default(false);
            $table->boolean('accepted')->nullable();
            $table->text('reply')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('feedback_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->boolean('review');
            $table->integer('reviewer_id')->nullable();
            $table->boolean('can_reply')->default(true);
            $table->timestamps();
        });

        $goodideaCategory = new FeedbackCategory([
            'title' => 'Good Ideas',
            'url' => 'goodideas',
            'review' => false,
            'can_reply' => true,
        ]);
        $goodideaCategory->save();

        if (Schema::hasTable('good_ideas')) {
            foreach (DB::table('good_ideas')->get() as $goodidea) {
                $new = new Feedback([
                    'user_id' => $goodidea->user_id,
                    'feedback_category_id' => $goodideaCategory->id,
                    'feedback' => $goodidea->idea,
                    'reviewed' => true,
                    'created_at' => $goodidea->created_at,
                ]);
                $new->save();
                foreach (DB::table('good_idea_votes')->where('good_idea_id', $goodidea->id)->get() as $like) {
                    $newLike = new FeedbackVote([
                        'user_id' => $like->user_id,
                        'feedback_id' => $new->id,
                        'vote' => $like->vote,
                        'created_at' => $goodidea->created_at,
                    ]);
                    $newLike->save();
                }
            }

            Schema::drop('good_ideas');
            Schema::drop('good_idea_votes');
        }

        $quoteCategory = new FeedbackCategory([
            'title' => 'Quotes',
            'url' => 'quotes',
            'review' => false,
            'can_reply' => false,
        ]);
        $quoteCategory->save();

        if (Schema::hasTable('quotes')) {
            foreach (DB::table('quotes')->get() as $quote) {
                $new = new Feedback([
                    'user_id' => $quote->user_id,
                    'feedback_category_id' => $quoteCategory->id,
                    'feedback' => $quote->quote,
                    'reviewed' => true,
                    'created_at' => $quote->created_at,
                ]);
                $new->save();
                foreach (DB::table('quotes_users')->where('quote_id', $quote->id)->get() as $like) {
                    $newLike = new FeedbackVote([
                        'user_id' => $like->user_id,
                        'feedback_id' => $new->id,
                        'vote' => 1,
                        'created_at' => $quote->created_at,
                    ]);
                    $newLike->save();
                }
            }

            Schema::drop('quotes');
            Schema::drop('quotes_users');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('quote');
            $table->timestamps();
        });

        Schema::create('quotes_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('quote_id');
            $table->timestamps();
        });

        $quotes = Feedback::query()->whereHas('category', function ($q) {
            $q->where('url', 'quotes');
        })->get();
        foreach ($quotes as $quote) {
            $newQuoteId = DB::table('quotes')->insertGetId(
                ['user_id' => $quote->user_id, 'quote' => $quote->feedback, 'created_at' => $quote->created_at, 'updated_at' => $quote->updated_at]
            );

            foreach ($quote->votes as $vote) {
                if ($vote->vote == 1) {
                    DB::table('quotes_users')->insert(
                        ['user_id' => $vote->user_id, 'quote_id' => $newQuoteId, 'created_at' => $vote->created_at, 'updated_at' => $vote->updated_at]
                    );
                }
            }
        }

        Schema::create('good_ideas', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('idea');
            $table->timestamps();
        });

        Schema::create('good_idea_votes', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('good_idea_id');
            $table->integer('vote');
            $table->timestamps();
        });

        $goodIdeas = Feedback::query()->whereHas('category', function ($q) {
            $q->where('url', 'goodideas');
        })->get();
        foreach ($goodIdeas as $goodIdea) {
            $newGoodIdeaId = DB::table('good_ideas')->insertGetId(
                ['user_id' => $goodIdea->user_id, 'idea' => $goodIdea->feedback, 'created_at' => $goodIdea->created_at, 'updated_at' => $goodIdea->updated_at]
            );

            foreach ($goodIdea->votes as $vote) {
                DB::table('good_idea_votes')->insert(
                    ['user_id' => $vote->user_id, 'good_idea_id' => $newGoodIdeaId, 'vote' => $vote->vote, 'created_at' => $vote->created_at, 'updated_at' => $vote->updated_at]
                );
            }
        }

        Schema::drop('feedback');
        Schema::drop('feedback_votes');
        Schema::drop('feedback_categories');
    }
}
