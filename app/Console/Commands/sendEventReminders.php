<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notifications to all event attendees that event is starts soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         *'attendees.user' : get the attendees with detail user relation
         * [A, B] -> from A time to B time
         */
        $events = Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])
            ->get();

        $eventCount = $events->count(); // counting data in collection
        $eventLabel = Str::plural('event', $eventCount);

        // show information on terminal
        $this->info("Found $eventCount $eventLabel");

        $events->each(
            fn($event) => $event->attendees->each(
                fn($attendee) => $attendee->user->notify(
                    new EventReminderNotification($event)
                )
            )
        );

        $this->info('Reminder notifications sent successfully!');
    }
}
