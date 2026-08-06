<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Active = 'Active';
    case Completed = 'Completed';
    case Terminated = 'Terminated';
    case Rejected = 'Rejected';
}
