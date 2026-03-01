<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ObservationItem;
use App\Enums\JenisFrekuensi;

class ConditionalItemService
{
    /**
     * Daftar item kondisional yang perlu dikonfirmasi setiap bulan
     */
    private const CONDITIONAL_ITEMS = [
        'mengisi_self_assessment' => 'Mengisi lembar self-assessment',
        'mengikuti_pramuka' => 'Mengikuti pramuka',
        'mengikuti_paket_abc' => 'Mengikuti pendidikan Paket A/B/C',
        'konseling_psikologi' => 'Mengikuti konseling psikologi',
        'rehabilitasi_sosial' => 'Mengikuti rehabilitasi sosial',
        'rehabilitasi_medis' => 'Mengikuti rehabilitasi medis',
        'merapikan_penampilan' => 'Mau merapikan rambut, janggut, dan kuku',
        'pernyataan_nkri' => 'Menandatangani pernyataan kesetiaan terhadap NKRI',
        'pernyataan_narkoba' => 'Menandatangani pernyataan tidak terlibat dalam jaringan narkoba',
    ];

    /**
     * Get all conditional items with their current status
     */
    public function getConditionalItems(): array
    {
        return self::CONDITIONAL_ITEMS;
    }

    /**
     * Check if modal should be shown for current month
     */
    public function shouldShowModal(Carbon $tanggalPenilaian): bool
    {
        $monthKey = $tanggalPenilaian->format('Y-m');

        // Check session - user opted to not show again this month
        if (session()->has("conditional_items_set_{$monthKey}")) {
            return false;
        }

        // Check if any conditional item already set for this month
        $alreadySet = ObservationItem::where('jenis_frekuensi', JenisFrekuensi::KONDISIONAL)
            ->where('bobot_last_set_month', $monthKey)
            ->exists();

        return !$alreadySet;
    }

    /**
     * Update bobot for conditional items based on user selection
     *
     * @param array $selections ['mengisi_self_assessment' => true, 'mengikuti_pramuka' => false, ...]
     * @param Carbon $tanggalPenilaian
     * @return array ['success_count' => int, 'failed' => array]
     */
    public function updateConditionalBobots(array $selections, Carbon $tanggalPenilaian): array
    {
        $monthKey = $tanggalPenilaian->format('Y-m');
        $successCount = 0;
        $failed = [];

        DB::beginTransaction();
        try {
            foreach ($selections as $key => $diselenggarakan) {
                if (!isset(self::CONDITIONAL_ITEMS[$key])) {
                    continue; // Skip unknown items
                }

                $itemName = self::CONDITIONAL_ITEMS[$key];

                // Find the observation item by name
                $item = ObservationItem::where('jenis_frekuensi', JenisFrekuensi::KONDISIONAL)
                    ->where('nama_item', 'LIKE', "%{$itemName}%")
                    ->first();

                if (!$item) {
                    $failed[] = $itemName;
                    Log::warning("Conditional item not found: {$itemName}");
                    continue;
                }

                // Update bobot: 1 if diselenggarakan, 0 if not
                $newBobot = $diselenggarakan ? 1.00 : 0.00;

                $item->update([
                    'bobot' => $newBobot,
                    'bobot_last_set_month' => $monthKey,
                ]);

                $successCount++;

                Log::info("Conditional item updated", [
                    'item' => $itemName,
                    'bobot' => $newBobot,
                    'month' => $monthKey,
                ]);
            }

            DB::commit();

            // Mark that conditional items have been set for this month
            session()->put("conditional_items_set_{$monthKey}", true);

            return [
                'success_count' => $successCount,
                'failed' => $failed,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update conditional items', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Reset conditional items for a new month
     * Called automatically when entering a new month
     */
    public function resetForNewMonth(Carbon $tanggalPenilaian): void
    {
        $monthKey = $tanggalPenilaian->format('Y-m');

        // Reset all conditional items that haven't been set this month
        ObservationItem::where('jenis_frekuensi', JenisFrekuensi::KONDISIONAL)
            ->where(function($q) use ($monthKey) {
                $q->whereNull('bobot_last_set_month')
                  ->orWhere('bobot_last_set_month', '!=', $monthKey);
            })
            ->update([
                'bobot' => DB::raw('bobot_default'),
            ]);

        Log::info("Conditional items reset for new month: {$monthKey}");
    }

    /**
     * Get current conditional items status for a month
     */
    public function getCurrentStatus(Carbon $tanggalPenilaian): array
    {
        $monthKey = $tanggalPenilaian->format('Y-m');
        $status = [];

        foreach (self::CONDITIONAL_ITEMS as $key => $itemName) {
            $item = ObservationItem::where('jenis_frekuensi', JenisFrekuensi::KONDISIONAL)
                ->where('nama_item', 'LIKE', "%{$itemName}%")
                ->first();

            if ($item) {
                $status[$key] = [
                    'name' => $itemName,
                    'diselenggarakan' => $item->bobot > 0,
                    'bobot' => (float) $item->bobot,
                    'last_set' => $item->bobot_last_set_month === $monthKey,
                ];
            }
        }

        return $status;
    }

    /**
     * Mark modal as "don't show again this month"
     */
    public function skipModalThisMonth(Carbon $tanggalPenilaian): void
    {
        $monthKey = $tanggalPenilaian->format('Y-m');
        session()->put("conditional_items_set_{$monthKey}", true);
    }
}
