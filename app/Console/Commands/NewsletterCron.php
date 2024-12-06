<?php

namespace App\Console\Commands;

use App\Mail\Newsletter as NewsletterMail;
use App\Models\EmailList;
use App\Models\Newsitem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class NewsletterCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:newslettercron {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cronjob that sends the weekly newsletter';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $newsletterlist = EmailList::query()->findOrFail(Config::integer('proto.weeklynewsletter'));

        $newsitem = Newsitem::query()->findOrFail($this->argument('id'));

        if (! $newsitem->is_weekly) {
            $this->error('This is not a weekly newsletter item!');

            return;
        }

        if ($newsitem->published_at != null) {
            $this->error('This newsletter has already been sent!');

            return;
        }

        $image_url = $newsitem->featuredImage?->generateImagePath(600, 300);

        $events = $newsitem->events;

        $this->info('Sending weekly newsletter to '.$newsletterlist->users->count().' people.');

        foreach ($newsletterlist->users as $user) {
            Mail::to($user)->queue((new NewsletterMail($user, $newsletterlist, $newsitem->content, $events, $image_url))->onQueue('low'));
        }

        $this->info('Done!');
    }
}
