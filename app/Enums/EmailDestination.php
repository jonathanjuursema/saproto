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
}