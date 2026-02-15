<?php

namespace App\Enums;

enum JenisFrekuensi: string
{
    case HARIAN     = 'Harian';
    case MINGGUAN1  = 'Mingguan1';
    case MINGGUAN2  = 'Mingguan2';
    case MINGGUAN3  = 'Mingguan3';
    case KONDISIONAL = 'Kondisional';
    case FIX        = 'Fix';
}
