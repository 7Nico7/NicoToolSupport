<?php

namespace App\Providers;

use App\Events\TicketActivityLogged;
use App\Events\TicketMoved;
use App\Listeners\LogTicketActivity;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TicketActivityLogged::class => [
            [LogTicketActivity::class, 'handleActivityLogged'],
        ],
        TicketMoved::class => [
            [LogTicketActivity::class, 'handleTicketMoved'],
        ],
    ];
}
