<?php

namespace App\Enums;

enum IsAlfredThereEnum: string
{
    case THERE = 'there';
    case AWAY = 'away';
    case UNKNOWN = 'unknown';
    case JUR = 'jur';
    case TEXT_ONLY = 'text';
}
