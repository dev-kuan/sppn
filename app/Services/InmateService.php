<?php

namespace App\Services;

use App\Models\Inmate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InmateService
{
    public function storeInmate(array $data): Inmate
    {
        DB::beginTransaction();

        try {
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
