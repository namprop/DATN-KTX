<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'Available';
    case Full = 'Full';
    case Maintenance = 'Maintenance';
}
