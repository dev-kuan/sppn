<?php

namespace App\Services;

use App\Models\Inmate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InmateService
{
    private function generateNoRegistrasi(): string
    {
        $monthYear = Carbon::now()->format('my'); // Contoh: 0326 untuk Maret 2026

        // Ambil nomor registrasi terakhir bulan ini
        $lastInmate = Inmate::whereYear('created_at', Carbon::now()->year)
                           ->whereMonth('created_at', Carbon::now()->month)
                           ->orderBy('no_registrasi', 'desc')
                           ->first();

        if ($lastInmate && preg_match('/REG-(\d{3})-' . $monthYear . '/', $lastInmate->no_registrasi, $matches)) {
            $lastNumber = intval($matches[1]);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'REG-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT) . '-' . $monthYear;
    }

    /**
     * Get nomor registrasi berikutnya (untuk preview di form)
     */
    public function getNextNoRegistrasi(): string
    {
        return $this->generateNoRegistrasi();
    }

    public function storeInmate(array $data): Inmate
    {
        DB::beginTransaction();
        try {
            // Generate nomor registrasi otomatis
            $data['no_registrasi'] = $this->generateNoRegistrasi();

            $inmate = Inmate::create($data);
            $this->logInmateActivity($inmate, 'created');

            DB::commit();
            return $inmate;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Data Narapidana gagal ditambahkan: ' . $e->getMessage());
            throw $e;
        }
    }
    public function updateInmate(Inmate $inmate, array $data):Inmate {
        DB::beginTransaction();
        try {
            $inmate->update($data);
            $this->logInmateActivity($inmate, 'updated');

            DB::commit();

            return $inmate;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Data Narapidana gagal diubah: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteInmate(Inmate $inmate) {
        DB::beginTransaction();
        try {
            $inmate->delete();

            $this->logInmateActivity($inmate, 'deleted');

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Data Narapidana gagal dihapus: ' . $e->getMessage());
            throw $e;
        }
    }

    public function restoreInmate(int $id): Inmate
    {
        // $this->authorize('delete-narapidana');

        DB::beginTransaction();
        try {
            $inmate = Inmate::withTrashed()->findOrFail($id);
            $inmate->restore();

            $this->logInmateActivity($inmate, 'restored');

            DB::commit();

            return $inmate;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Data Narapidana gagal dipulihkan: ' . $e->getMessage());
            throw $e;
        }
    }

protected function logInmateActivity(Inmate $inmate, string $action): void
    {
        $messages = [
            'created' => 'Data Narapidana baru ditambahkan: ' . $inmate->nama,
            'updated' => 'Data Narapidana diperbarui: ' . $inmate->nama,
            'deleted' => 'Data Narapidana dihapus: ' . $inmate->nama,
            'restored' => 'Data narapidana dipulihkan: ' . $inmate->nama,
        ];

        activity()
            ->performedOn($inmate)
            ->causedBy(auth()->user())
            ->log($messages[$action] ?? 'Aktivitas narapidana: ' . $action);
    }
}
