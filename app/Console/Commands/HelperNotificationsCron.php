<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\DailyHelperMail;
use App\Models\HelpingCommittee;
use App\Models\User;

class HelperNotificationsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:helpernotificationcron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cronjob that sends the helper notifications.';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::query()
            ->whereHas('member')
            ->get()
            ->filter(function ($value, $key) {
                return $value->isActiveMember();
            });

        $handledHelpIds = [];

        foreach ($users as $user) {

            $events = [];

            foreach ($user->committees as $committee) {
                $helps = HelpingCommittee::query()->where('committee_id', $committee->id)
                    ->where('notification_sent', '0') //check if the event is not yet notified
                    ->whereHas('activity', function ($q) {
                        $q->whereHas('event', function ($w) {
                            $w->where('start', '>', \Carbon::now()->timestamp); //check if the event is in the future
                        });
                    })
                    ->whereHas('committee', function ($q) use ($user) {
                        $q->whereHas('users', function ($w) use ($user) {
                            $w->where('users.id', $user->id); //check if the user is still in the active committee members
                        });
                    })
                    ->with('activity')
                    ->with('activity.event') //load both activity and the event with the HelpingCommittee
                    ->get()
                    ->sortBy('activity.event.start')
                    ->filter(function ($helpingCommittee) {
                        return $helpingCommittee->amount > $helpingCommittee->helperCount(); //only include the activities that have not yet have enough helpers
                    });

                foreach ($helps as $help) {
                    if (! isset($events[$help->activity->event->id])) {
                        $events[$help->activity->event->id] = new \stdClass();
                        $events[$help->activity->event->id]->help = [];
                    }

                    $helpInfo = new \stdClass();
                    $helpInfo->amount = $help->amount;
                    $helpInfo->committeeName = $help->committee->name;

                    $events[$help->activity->event->id]->help[] = $helpInfo;

                    if (! in_array($help->id, $handledHelpIds)) {
                        $handledHelpIds[] = $help->id;
                    }

                }
            }

            if (count($events) > 0) {
                $this->info('Sending notification mail to '.$user->name.' with '.count($events).' events.');
                Mail::to($user)->queue((new DailyHelperMail($user, $events))->onQueue('low'));
            }
        }

        foreach ($handledHelpIds as $handledHelpId) {
            $handledHelp = HelpingCommittee::find($handledHelpId);
            $handledHelp->notification_sent = true;
            $handledHelp->save();
        }

        $this->info('Done.');
    }
}
