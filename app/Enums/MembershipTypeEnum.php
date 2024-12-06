<?php

namespace App\Enums;

enum MembershipTypeEnum: int
{
    case PENDING = 0;
    case REGULAR = 1;
    case PET = 2;
    case LIFELONG = 3;
    case HONORARY = 4;
    case DONOR = 5;
}
