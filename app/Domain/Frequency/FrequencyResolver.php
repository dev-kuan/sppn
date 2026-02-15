<?php

namespace App\Domain\Frequency;

use App\Models\ObservationItem;
use App\Enums\JenisFrekuensi;
use Carbon\Carbon;

class FrequencyResolver
{
    public static function resolve(
        ObservationItem $item,
        Carbon $tanggalPenilaian,
        bool $kegiatanDiselenggarakan = false
    ): int {
        $day = $tanggalPenilaian->day;
        $daysInMonth = $tanggalPenilaian->daysInMonth;

        return match ($item->jenis_frekuensi) {

            JenisFrekuensi::HARIAN =>
                $daysInMonth,

            JenisFrekuensi::MINGGUAN1 =>
                self::resolveMingguan($day, [7=>4, 14=>3, 21=>2, 31=>1]),

            JenisFrekuensi::MINGGUAN2 =>
                self::resolveMingguan($day, [7=>8, 14=>6, 21=>4, 31=>2]),

            JenisFrekuensi::MINGGUAN3 =>
                self::resolveMingguan($day, [7=>20, 14=>15, 21=>10, 31=>2]),

            JenisFrekuensi::KONDISIONAL =>
                $kegiatanDiselenggarakan ? 1 : 0,

            JenisFrekuensi::FIX =>
                1,

            default => 0,
        };
    }

    private static function resolveMingguan(int $day, array $rules): int
    {
        foreach ($rules as $maxDay => $value) {
            if ($day <= $maxDay) {
                return $value;
            }
        }

        return 0;
    }
}
