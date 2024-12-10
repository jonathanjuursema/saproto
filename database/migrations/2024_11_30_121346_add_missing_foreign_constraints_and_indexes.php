<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // todo: check all onDeletes, make a migration to all bigints
        Schema::table('achievements_users', function (Blueprint $table) {
            //if a user gets deleted, also delete its achievements
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            //if an achievement gets deleted, also delete its achievements
            $table->bigInteger('achievement_id')->unsigned()->index()->change();
            $table->foreign('achievement_id')->references('id')->on('achievement')->cascadeOnDelete();
        });

        Schema::table('activities_users', function (Blueprint $table) {
            $table->bigInteger('committees_activities_id')->unsigned()->index()->change();
            //if a committee gets deleted, also delete all the helping committee members who where helping in that committee
            $table->foreign('committees_activities_id')->references('id')->on('committees')->cascadeOnDelete();
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->change();
            //if a user gets deleted, also delete the address
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('alias', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->change();
            //if a user gets deleted, also delete the alias
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->change();
            //if a user gets deleted, also delete the bank account
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('codex_codex_song', function (Blueprint $table) {
            //if the codex is deleted, also delete the pivot with the songs
            $table->bigInteger('codex')->unsigned()->index()->change();
            $table->foreign('codex')->references('id')->on('codex_codices')->cascadeOnDelete();

            //if the song is deleted, also delete the pivot with the codices
            $table->bigInteger('song')->unsigned()->index()->change();
            $table->foreign('song')->references('id')->on('codex_songs')->cascadeOnDelete();
        });

        Schema::table('codex_codex_text', function (Blueprint $table) {
            //if the codex is deleted, also delete the pivot with the texts
            $table->bigInteger('codex')->unsigned()->index()->change();
            $table->foreign('codex')->references('id')->on('codex_codices')->cascadeOnDelete();

            //if the text is deleted, also delete the pivot with the codices
            $table->bigInteger('text_id')->unsigned()->index()->change();
            $table->foreign('text_id')->references('id')->on('codex_texts')->cascadeOnDelete();
        });

        Schema::table('codex_texts', function (Blueprint $table) {
            //do not delete the type if it is used in a text
            $table->bigInteger('type_id')->unsigned()->index()->change();
            $table->foreign('type_id')->references('id')->on('codex_text_types')->noActionOnDelete();
        });

        Schema::table('committees_activities', function (Blueprint $table) {
            $table->bigInteger('activity_id')->unsigned()->index()->change();
            //if an activity is deleted, also delete the pivots with the committees who organized it
            $table->foreign('activity_id')->references('id')->on('activities')->cascadeOnDelete();

            $table->bigInteger('committee_id')->unsigned()->index()->change();
            //if an committee is deleted, also delete the pivots with the activities it has organized
            $table->foreign('committee_id')->references('id')->on('committees')->cascadeOnDelete();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->bigInteger('image_id')->unsigned()->nullable()->index()->change();
            //the company should not be deleted if the image somehow is deleted
            $table->foreign('image_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('committees_users', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->change();
            //if a user is deleted, also delete the pivot with the committees
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->bigInteger('committee_id')->unsigned()->index()->change();
            //if a committee is deleted, also delete the pivot with the users
            $table->foreign('committee_id')->references('id')->on('committees')->cascadeOnDelete();
        });

        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->bigInteger('event_id')->unsigned()->nullable()->index()->change();
            //if an event is destroyed, set the relation to null so the dinnerform stays existing
            $table->foreign('event_id')->references('id')->on('events')->nullOnDelete();

            $table->bigInteger('ordered_by_user_id')->unsigned()->nullable()->index()->change();
            //if an user who ordered is destroyed, set the relation to null so the dinnerform stays existing
            $table->foreign('ordered_by_user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('dinnerform_orderline', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->change();
            //if a user is destroyed delete all dinnerformorderlines associated
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->bigInteger('dinnerform_id')->unsigned()->index()->change();
            //if a dinnerform is destroyed also delete all orders
            $table->foreign('dinnerform_id')->references('id')->on('dinnerforms')->cascadeOnDelete();
        });

        Schema::table('emails_events', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->index()->change();
            //if an email is destroyed, also delete the pivot with the events featured in the email
            $table->foreign('email_id')->references('id')->on('emails')->cascadeOnDelete();

            $table->bigInteger('event_id')->unsigned()->index()->change();
            //if an event is destroyed, also delete the pivot with the emails they are featured in
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });

        Schema::table('emails_files', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->index()->change();
            //if an email is destroyed, also delete the pivot with the files featured in the email
            $table->foreign('email_id')->references('id')->on('emails')->cascadeOnDelete();

            $table->bigInteger('file_id')->unsigned()->index()->change();
            //if a file is destroyed, also delete the pivot with the emails they are featured in
            $table->foreign('file_id')->references('id')->on('files')->cascadeOnDelete();
        });

        Schema::table('emails_lists', function (Blueprint $table) {
            $table->bigInteger('email_id')->unsigned()->index()->change();
            //if an email is destroyed, also delete the pivot with the lists
            $table->foreign('email_id')->references('id')->on('emails')->cascadeOnDelete();

            $table->bigInteger('list_id')->unsigned()->index()->change();
            //if a list is destroyed, also delete the pivot with the emails
            $table->foreign('list_id')->references('id')->on('mailinglists')->cascadeOnDelete();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('category_id')->unsigned()->nullable()->index()->change();
            //if a category is destroyed, set the relation to null so the event stays existing
            $table->foreign('category_id')->references('id')->on('event_categories')->nullOnDelete();

            $table->bigInteger('image_id')->unsigned()->nullable()->index()->change();
            //if an image is destroyed, set the relation to null so the event stays existing
            $table->foreign('image_id')->references('id')->on('files')->nullOnDelete();

            $table->bigInteger('committee_id')->unsigned()->nullable()->index()->change();
            //if a committee is destroyed, set the relation to null so the event stays existing
            $table->foreign('committee_id')->references('id')->on('committees')->nullOnDelete();
        });

        Schema::table('event_newsitem', function (Blueprint $table) {
            //if an event is destroyed, also delete the pivot with the newsitems featured in the event
            $table->bigInteger('event_id')->unsigned()->index()->change();
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();

            $table->bigInteger('newsitem_id')->unsigned()->index()->change();
            //if a newsitem is destroyed, also delete the pivot with the events they are featured in
            $table->foreign('newsitem_id')->references('id')->on('newsitems')->cascadeOnDelete();
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->change();
            //if the user is destroyed, also delete their feedback
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->bigInteger('feedback_category_id')->unsigned()->index()->change();
            //if the feedback category is destroyed, also delete the feedback
            $table->foreign('feedback_category_id')->references('id')->on('feedback_categories')->cascadeOnDelete();
        });

        Schema::table('feedback_categories', function (Blueprint $table) {
            $table->bigInteger('reviewer_id')->unsigned()->nullable()->index()->change();
            //if the reviewer is destroyed, set the relation to null so the feedback category stays existing
            $table->foreign('reviewer_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('feedback_votes', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->index()->change();
            //if the user is destroyed, also delete their feedback votes
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->bigInteger('feedback_id')->unsigned()->index()->change();
            //if the feedback is destroyed, also delete the feedback votes
            $table->foreign('feedback_id')->references('id')->on('feedback')->cascadeOnDelete();
        });

        Schema::table('headerimages', function (Blueprint $table) {
            //if an image is destroyed, also delete the header image
            $table->bigInteger('image_id')->unsigned()->index()->change();
            $table->foreign('image_id')->references('id')->on('files')->cascadeOnDelete();

            //if the user who credited the image is destroyed, set the relation to null so the header image stays existing
            $table->bigInteger('credit_id')->unsigned()->nullable()->index()->change();
            $table->foreign('credit_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('joboffers', function (Blueprint $table) {
            //if a company is destroyed, also delete the job offer
            $table->bigInteger('company_id')->unsigned()->index()->change();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });

        Schema::table('leaderboards', function (Blueprint $table) {
            //if a committee is destroyed, also delete the leaderboard
            $table->bigInteger('committee_id')->unsigned()->index()->change();
            $table->foreign('committee_id')->references('id')->on('committees')->cascadeOnDelete();
        });

        Schema::table('leaderboards_entries', function (Blueprint $table) {
            //if a user is destroyed, also delete the leaderboard entry
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            //if a leaderboard is destroyed, delete the leaderboard entry
            $table->bigInteger('leaderboard_id')->unsigned()->index()->change();
            $table->foreign('leaderboard_id')->references('id')->on('leaderboards')->cascadeOnDelete();
        });

        Schema::table('members', function (Blueprint $table) {
            //the user should not be able to be deleted if it has a membership
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->noActionOnDelete();

            //if somehow a membership form is deleted, the membership should not be deleted
            $table->bigInteger('membership_form_id')->nullable()->unsigned()->index()->change();
            $table->foreign('membership_form_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('menuitems', function (Blueprint $table) {
            //if a page is destroyed, also delete the menu item
            $table->bigInteger('page_id')->unsigned()->index()->change();
            $table->foreign('page_id')->references('id')->on('pages')->cascadeOnDelete();

            //if a parent menu item is destroyed, also delete the child menu item
            $table->bigInteger('parent')->unsigned()->index()->nullable()->change();
            $table->foreign('parent')->references('id')->on('menuitems')->cascadeOnDelete();
        });

        Schema::table('mollie_transactions', function (Blueprint $table) {
            //if a user is destroyed, also delete the mollie transaction
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('narrowcasting', function (Blueprint $table) {
            //if an image is destroyed, also delete the narrowcasting
            $table->bigInteger('image_id')->unsigned()->index()->change();
            $table->foreign('image_id')->references('id')->on('files')->cascadeOnDelete();
        });

        Schema::table('newsitems', function (Blueprint $table) {
            //if a user is destroyed, do not delete the newsitem, but set the relation to null
            $table->bigInteger('user_id')->unsigned()->nullable()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            //if an image is destroyed, do not delete the newsitem, but set the relation to null
            $table->bigInteger('featured_image_id')->unsigned()->nullable()->index()->change();
            $table->foreign('featured_image_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('orderlines', function (Blueprint $table) {
            //if a user is destroyed, also delete the orderline
            $table->bigInteger('user_id')->unsigned()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            //if the cashier is destroyed, do not delete the orderline, but set the relation to null
            $table->bigInteger('cashier_id')->unsigned()->nullable()->index()->change();
            $table->foreign('cashier_id')->references('id')->on('users')->nullOnDelete();

            //prevent the product from being deleted if it is in an orderline
            $table->bigInteger('product_id')->unsigned()->index()->change();
            $table->foreign('product_id')->references('id')->on('products')->noActionOnDelete();

            //if the mollie transaction is destroyed, set the relation to null so the orderline stays existing
            $table->bigInteger('payed_with_mollie')->unsigned()->nullable()->index()->change();
            $table->foreign('payed_with_mollie')->references('id')->on('mollie_transactions')->nullOnDelete();

            //if the withdrawal is destroyed, set the relation to null so the orderline stays existing
            $table->bigInteger('payed_with_withdrawal')->unsigned()->nullable()->index()->change();
            $table->foreign('payed_with_withdrawal')->references('id')->on('withdrawals')->nullOnDelete();
        });

        Schema::table('pages', function (Blueprint $table) {
            //if a featured image is destroyed, do not delete the page, but set the relation to null
            $table->bigInteger('featured_image_id')->unsigned()->nullable()->index()->change();
            $table->foreign('featured_image_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('passwordstore', function (Blueprint $table) {
            //if a permission is destroyed, also delete the related passwords
            $table->bigInteger('permission_id')->unsigned()->index()->change();
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
        });

        Schema::table('photos', function (Blueprint $table) {
            //if the file is destroyed, also delete the photo
            $table->bigInteger('file_id')->unsigned()->index()->change();
            $table->foreign('file_id')->references('id')->on('files')->cascadeOnDelete();

            //Prevent the album from being deleted if it has photos
            $table->bigInteger('album_id')->unsigned()->index()->change();
            $table->foreign('album_id')->references('id')->on('photo_albums')->noActionOnDelete();
        });

        Schema::table('photo_albums', function (Blueprint $table) {
            //if the thumbnail is destroyed, do not delete the album, but set the relation to null
            $table->bigInteger('thumb_id')->unsigned()->nullable()->index()->change();
            $table->foreign('thumb_id')->references('id')->on('photos')->nullOnDelete();

            //if the event is destroyed, do not delete the album, but set the relation to null
            $table->bigInteger('event_id')->unsigned()->nullable()->index()->change();
            $table->foreign('event_id')->references('id')->on('events')->nullOnDelete();
        });

        Schema::table('photo_likes', function (Blueprint $table) {
            //if the photo is destroyed, also delete the like
            $table->bigInteger('photo_id')->unsigned()->index()->change();
            $table->foreign('photo_id')->references('id')->on('photos')->cascadeOnDelete();

            //if the user is destroyed, do not delete the like, but set the relation to null
            $table->bigInteger('user_id')->unsigned()->nullable()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('playedvideos', function (Blueprint $table) {
            //if the user is destroyed, also delete their played videos
            $table->bigInteger('user_id')->unsigned()->nullable()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            //if the account is destroyed, do not delete the product, but set the relation to null
            $table->bigInteger('account_id')->unsigned()->nullable()->index()->change();
            $table->foreign('account_id')->references('id')->on('accounts')->nullOnDelete();

            //if the image is destroyed, do not delete the product, but set the relation to null
            $table->bigInteger('image_id')->unsigned()->nullable()->index()->change();
            $table->foreign('image_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('products_categories', function (Blueprint $table) {
            //if the product is destroyed, also delete the pivot with the categories
            $table->bigInteger('product_id')->unsigned()->index()->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            //if the category is destroyed, also delete the pivot with the products
            $table->bigInteger('category_id')->unsigned()->index()->change();
            $table->foreign('category_id')->references('id')->on('product_categories')->cascadeOnDelete();
        });

        Schema::table('product_wallstreet_drink', function (Blueprint $table) {
            //if the product is destroyed, also delete the pivot with the wallstreet drinks
            $table->bigInteger('product_id')->unsigned()->index()->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            //if the wallstreet drink is destroyed, also delete the pivot with the products
            $table->bigInteger('wallstreet_drink_id')->unsigned()->index()->change();
            $table->foreign('wallstreet_drink_id')->references('id')->on('wallstreet_drink')->cascadeOnDelete();
        });

        Schema::table('qrauth_requests', function (Blueprint $table) {
            //if the user is destroyed, also delete their qrauth requests
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('rfid', function (Blueprint $table) {
            //if the user is destroyed, also delete their rfid
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('sessions', function (Blueprint $table) {
            //if the user is destroyed, also delete their sessions
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('soundboard_sounds', function (Blueprint $table) {
            //if the file is destroyed, also delete the sound
            $table->bigInteger('file_id')->unsigned()->index()->change();
            $table->foreign('file_id')->references('id')->on('files')->cascadeOnDelete();
        });

        Schema::table('stock_mutations', function (Blueprint $table) {
            //todo: check if the product should be deleted if the user_id is deleted
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->noActionOnDelete();

            //if the product is destroyed, also delete the stock mutation
            $table->bigInteger('product_id')->unsigned()->index()->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('tempadmins', function (Blueprint $table) {
            //if the user is destroyed, also delete their temp admin
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('tickets', function (Blueprint $table) {
            //Do not let the event be deleted if it has tickets
            $table->bigInteger('event_id')->unsigned()->index()->change();
            $table->foreign('event_id')->references('id')->on('events')->noActionOnDelete();

            //do not let the product be deleted if it has tickets
            $table->bigInteger('product_id')->unsigned()->index()->change();
            $table->foreign('product_id')->references('id')->on('products')->noActionOnDelete();
        });

        Schema::table('ticket_purchases', function (Blueprint $table) {
            //if the ticket is destroyed, also delete the ticket purchases
            $table->bigInteger('ticket_id')->unsigned()->index()->change();
            $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnDelete();

            //if the orderline is destroyed, also delete the ticket purchases
            $table->bigInteger('orderline_id')->unsigned()->index()->change();
            $table->foreign('orderline_id')->references('id')->on('orderlines')->cascadeOnDelete();

            //if the user is destroyed, also delete their ticket purchases
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('tokens', function (Blueprint $table) {
            //if the user is destroyed, also delete their tokens
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            //if the file is destroyed, do not delete the user, but set the relation to null
            $table->bigInteger('image_id')->unsigned()->nullable()->index()->change();
            $table->foreign('image_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('users_mailinglists', function (Blueprint $table) {
            //if the user is destroyed, also delete the pivot with the mailinglists
            $table->bigInteger('list_id')->unsigned()->index()->change();
            $table->foreign('list_id')->references('id')->on('emails_lists')->cascadeOnDelete();

            //if the mailinglist is destroyed, also delete the pivot with the users
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('user_welcome', function (Blueprint $table) {
            //if the user is destroyed, also delete their welcome message
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('ut_accounts', function (Blueprint $table) {
            //if the member is destroyed, also delete their ut account
            $table->bigInteger('member_id')->unsigned()->index()->change();
            $table->foreign('member_id')->references('id')->on('members')->cascadeOnDelete();
        });

        Schema::table('wallstreet_drink_event', function (Blueprint $table) {
            //if the event is destroyed, also delete the wallstreet drink event that happened
            $table->bigInteger('wallstreet_drink_id')->unsigned()->index()->change();
            $table->foreign('wallstreet_drink_id')->references('id')->on('wallstreet_drink')->cascadeOnDelete();

            //if the wallstreet drink event is destroyed, also delete the events that happened
            $table->bigInteger('wallstreet_drink_events_id')->unsigned()->index()->change();
            $table->foreign('wallstreet_drink_events_id')->references('id')->on('wallstreet_drink_events')->cascadeOnDelete();
        });

        Schema::table('wallstreet_drink_events', function (Blueprint $table) {
            //if the image is destroyed, do not delete the wallstreet drink event, but set the relation to null
            $table->bigInteger('image_id')->unsigned()->nullable()->index()->change();
            $table->foreign('image_id')->references('id')->on('files')->nullOnDelete();
        });

        Schema::table('wallstreet_drink_event_product', function (Blueprint $table) {
            //if the wallstreet drink event is destroyed, also delete the pivot with the products
            $table->bigInteger('wallstreet_drink_event_id')->unsigned()->index()->change();
            $table->foreign('wallstreet_drink_event_id')->references('id')->on('wallstreet_drink_events')->cascadeOnDelete();

            //if the product is destroyed, also delete the pivot with the wallstreet drink events
            $table->bigInteger('product_id')->unsigned()->index()->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('wallstreet_drink_prices', function (Blueprint $table) {
            //if the wallstreet drink is destroyed, also delete the prices
            $table->bigInteger('wallstreet_drink_id')->unsigned()->index()->change();
            $table->foreign('wallstreet_drink_id')->references('id')->on('wallstreet_drink')->cascadeOnDelete();

            //if the product is destroyed, also delete the prices
            $table->bigInteger('product_id')->unsigned()->index()->change();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('withdrawals_failed', function (Blueprint $table) {
            //if the withdrawal is destroyed, also delete the failed withdrawal
            $table->bigInteger('withdrawal_id')->unsigned()->index()->change();
            $table->foreign('withdrawal_id')->references('id')->on('withdrawals')->cascadeOnDelete();

            //if the user is destroyed, also delete the failed withdrawal
            $table->bigInteger('user_id')->unsigned()->index()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            //do not let the failed withdrawal be deleted if it has an orderline
            $table->bigInteger('correction_orderline_id')->unsigned()->index()->change();
            $table->foreign('correction_orderline_id')->references('id')->on('orderlines')->noActionOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //reverse the up function
        Schema::table('achievements_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['achievement_id']);
            $table->dropIndex(['achievement_id']);
        });

        Schema::table('activities_users', function (Blueprint $table) {
            $table->dropForeign(['committees_activities_id']);
            $table->dropIndex(['committees_activities_id']);
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('alias', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('bankaccounts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('codex_codex_song', function (Blueprint $table) {
            $table->dropForeign(['codex']);
            $table->dropIndex(['codex']);

            $table->dropForeign(['song']);
            $table->dropIndex(['song']);
        });

        Schema::table('codex_codex_text', function (Blueprint $table) {
            $table->dropForeign(['codex']);
            $table->dropIndex(['codex']);

            $table->dropForeign(['text_id']);
            $table->dropIndex(['text_id']);
        });

        Schema::table('codex_texts', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropIndex(['type_id']);
        });

        Schema::table('committees_activities', function (Blueprint $table) {
            $table->dropForeign(['activity_id']);
            $table->dropIndex(['activity_id']);

            $table->dropForeign(['committee_id']);
            $table->dropIndex(['committee_id']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropIndex(['image_id']);
        });

        Schema::table('committees_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['committee_id']);
            $table->dropIndex(['committee_id']);
        });

        Schema::table('dinnerforms', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropIndex(['event_id']);

            $table->dropForeign(['ordered_by_user_id']);
            $table->dropIndex(['ordered_by_user_id']);
        });

        Schema::table('dinnerform_orderline', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['dinnerform_id']);
            $table->dropIndex(['dinnerform_id']);
        });

        Schema::table('emails_events', function (Blueprint $table) {
            $table->dropForeign(['email_id']);
            $table->dropIndex(['email_id']);

            $table->dropForeign(['event_id']);
            $table->dropIndex(['event_id']);
        });

        Schema::table('emails_files', function (Blueprint $table) {
            $table->dropForeign(['email_id']);
            $table->dropIndex(['email_id']);

            $table->dropForeign(['file_id']);
            $table->dropIndex(['file_id']);
        });

        Schema::table('emails_lists', function (Blueprint $table) {
            $table->dropForeign(['email_id']);
            $table->dropIndex(['email_id']);

            $table->dropForeign(['list_id']);
            $table->dropIndex(['list_id']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);

            $table->dropForeign(['image_id']);
            $table->dropIndex(['image_id']);

            $table->dropForeign(['committee_id']);
            $table->dropIndex(['committee_id']);
        });

        Schema::table('event_newsitem', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropIndex(['event_id']);

            $table->dropForeign(['newsitem_id']);
            $table->dropIndex(['newsitem_id']);
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['feedback_category_id']);
            $table->dropIndex(['feedback_category_id']);
        });

        Schema::table('feedback_categories', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropIndex(['reviewer_id']);
        });

        Schema::table('feedback_votes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['feedback_id']);
            $table->dropIndex(['feedback_id']);
        });

        Schema::table('headerimages', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropIndex(['image_id']);

            $table->dropForeign(['credit_id']);
            $table->dropIndex(['credit_id']);
        });

        Schema::table('joboffers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['company_id']);
        });

        Schema::table('leaderboards', function (Blueprint $table) {
            $table->dropForeign(['committee_id']);
            $table->dropIndex(['committee_id']);
        });

        Schema::table('leaderboards_entries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['leaderboard_id']);
            $table->dropIndex(['leaderboard_id']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['membership_form_id']);
            $table->dropIndex(['membership_form_id']);
        });

        Schema::table('menuitems', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropIndex(['page_id']);

            $table->dropForeign(['parent']);
            $table->dropIndex(['parent']);
        });

        Schema::table('mollie_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('narrowcasting', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropIndex(['image_id']);
        });

        Schema::table('newsitems', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['featured_image_id']);
            $table->dropIndex(['featured_image_id']);
        });

        Schema::table('orderlines', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['cashier_id']);
            $table->dropIndex(['cashier_id']);

            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);

            $table->dropForeign(['payed_with_mollie']);
            $table->dropIndex(['payed_with_mollie']);

            $table->dropForeign(['payed_with_withdrawal']);
            $table->dropIndex(['payed_with_withdrawal']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropIndex(['featured_image_id']);
        });

        Schema::table('passwordstore', function (Blueprint $table) {
            $table->dropForeign(['permission_id']);
            $table->dropIndex(['permission_id']);
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
            $table->dropIndex(['file_id']);

            $table->dropForeign(['album_id']);
            $table->dropIndex(['album_id']);
        });

        Schema::table('photo_albums', function (Blueprint $table) {
            $table->dropForeign(['thumb_id']);
            $table->dropIndex(['thumb_id']);

            $table->dropForeign(['event_id']);
            $table->dropIndex(['event_id']);
        });

        Schema::table('photo_likes', function (Blueprint $table) {
            $table->dropForeign(['photo_id']);
            $table->dropIndex(['photo_id']);

            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('playedvideos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropIndex(['account_id']);

            $table->dropForeign(['image_id']);
            $table->dropIndex(['image_id']);
        });

        Schema::table('products_categories', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);

            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
        });

        Schema::table('product_wallstreet_drink', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);

            $table->dropForeign(['wallstreet_drink_id']);
            $table->dropIndex(['wallstreet_drink_id']);
        });

        Schema::table('qrauth_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('rfid', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('soundboard_sounds', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
            $table->dropIndex(['file_id']);
        });

        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('tempadmins', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropIndex(['event_id']);

            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('ticket_purchases', function (Blueprint $table) {
            $table->dropForeign(['ticket_id']);
            $table->dropIndex(['ticket_id']);

            $table->dropForeign(['orderline_id']);
            $table->dropIndex(['orderline_id']);

            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('tokens', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropIndex(['image_id']);
        });

        Schema::table('users_mailinglists', function (Blueprint $table) {
            $table->dropForeign(['list_id']);
            $table->dropIndex(['list_id']);

            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('user_welcome', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('ut_accounts', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropIndex(['member_id']);
        });

        Schema::table('wallstreet_drink_event', function (Blueprint $table) {
            $table->dropForeign(['wallstreet_drink_id']);
            $table->dropIndex(['wallstreet_drink_id']);

            $table->dropForeign(['wallstreet_drink_events_id']);
            $table->dropIndex(['wallstreet_drink_events_id']);
        });

        Schema::table('wallstreet_drink_events', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropIndex(['image_id']);
        });

        Schema::table('wallstreet_drink_event_product', function (Blueprint $table) {
            $table->dropForeign(['wallstreet_drink_event_id']);
            $table->dropIndex(['wallstreet_drink_event_id']);

            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('wallstreet_drink_prices', function (Blueprint $table) {
            $table->dropForeign(['wallstreet_drink_id']);
            $table->dropIndex(['wallstreet_drink_id']);

            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('withdrawals_failed', function (Blueprint $table) {
            $table->dropForeign(['withdrawal_id']);
            $table->dropIndex(['withdrawal_id']);

            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);

            $table->dropForeign(['correction_orderline_id']);
            $table->dropIndex(['correction_orderline_id']);
        });
    }
};
