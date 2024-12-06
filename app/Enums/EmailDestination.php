<?php

namespace App\Enums;

enum EmailDestination: int
{
    case NO_DESTINATION = 0;
    case ALL_USERS = 1;
    case ALL_MEMBERS = 2;
    case PENDING_MEMBERS = 3;
    case ACTIVE_MEMBERS = 4;
    case EMAIL_LISTS = 5;

    case EVENT = 6;
    case EVENT_WITH_BACKUP = 7;
    case SPECIFIC_USERS = 8;

    public function text(): string
    {
        return match ($this) {
            EmailDestination::ALL_USERS => 'all users',
            EmailDestination::ALL_MEMBERS => 'all members',
            EmailDestination::PENDING_MEMBERS => 'all pending members',
            EmailDestination::ACTIVE_MEMBERS => 'all active members',
            EmailDestination::EMAIL_LISTS => 'list(s)',
            EmailDestination::EVENT => 'event(s)',
            EmailDestination::EVENT_WITH_BACKUP => 'event(s) with backup',
            EmailDestination::NO_DESTINATION => 'no destination',
            EmailDestination::SPECIFIC_USERS => 'specific users',
        };
    }
}
