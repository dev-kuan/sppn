<?php

namespace App\Http\Controllers;

use App\Models\Inmate;
use App\Models\CrimeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InmateController extends Controller
{
    public function index(Request $request)
    {
        // $this->authorize('view-narapidana');

        $query = Inmate::with('crimeType');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by crime type
        if ($request->has('crime_type') && $request->crime_type != '') {
            $query->where('crime_type_id', $request->crime_type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $inmates = $query->paginate(15)->withQueryString();
        $crimeTypes = CrimeType::all();

        return view('inmates.index', compact('inmates', 'crimeTypes'));
    }

    public function create()
    {
        // $this->authorize('create-narapidana');

        $crimeTypes = CrimeType::all();

        return view('inmates.create', compact('crimeTypes'));
    }

    public function store(Request $request)
    {
        // $this->authorize('create-narapidana');

        $validated = $request->validate([
            'no_registrasi' => 'required|string|max:255|unique:inmates,no_registrasi',
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'agama' => 'required|string|max:100',
            'tingkat_pendidikan' => 'nullable|string|max:100',
            'pekerjaan_terakhir' => 'nullable|string|max:100',
            'lama_pidana_bulan' => 'required|integer|min:1',
            'sisa_pidana_bulan' => 'required|integer|min:0',
            'jumlah_residivisme' => 'nullable|integer|min:0',
            'catatan_kesehatan' => 'nullable|string',
            'pelatihan' => 'nullable|string|max:255',
            'program_kerja' => 'nullable|string|max:255',
            'crime_type_id' => 'required|exists:crime_types,id',
            'tanggal_masuk' => 'required|date',
            'tanggal_bebas' => 'nullable|date|after:tanggal_masuk',
        ]);

        DB::beginTransaction();
        try {
            $inmate = Inmate::create($validated);

            activity()
                ->performedOn($inmate)
                ->causedBy(auth()->user())
                ->log('Narapidana baru ditambahkan: ' . $inmate->nama);

            DB::commit();

            return redirect()->route('inmates.show', $inmate)
                ->with('success', 'Data narapidana berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating inmate: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function show(Inmate $inmate)
    {
        // $this->authorize('view-narapidana');

        $inmate->load(['crimeType', 'assessments' => function ($query) {
            $query->latest()->limit(5);
        }]);

        // Get latest assessment
        $latestAssessment = $inmate->assessments()
            ->diterima()
            ->latest('tanggal_penilaian')
            ->first();

        return view('inmates.show', compact('inmate', 'latestAssessment'));
    }

    public function edit(Inmate $inmate)
    {
        // $this->authorize('edit-narapidana');

        $crimeTypes = CrimeType::all();
        $inmate->tanggal_masuk = $inmate->tanggal_masuk? $inmate->tanggal_masuk->format('Y-m-d'): null;

        return view('inmates.edit', compact('inmate', 'crimeTypes'));
    }

    public function update(Request $request, Inmate $inmate)
    {
        // $this->authorize('edit-narapidana');

        $validated = $request->validate([
            'no_registrasi' => 'required|string|max:255|unique:inmates,no_registrasi,' . $inmate->id,
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'agama' => 'required|string|max:100',
            'tingkat_pendidikan' => 'nullable|string|max:100',
            'pekerjaan_terakhir' => 'nullable|string|max:100',
            'lama_pidana_bulan' => 'required|integer|min:1',
            'sisa_pidana_bulan' => 'required|integer|min:0',
            'jumlah_residivisme' => 'nullable|integer|min:0',
            'catatan_kesehatan' => 'nullable|string',
            'pelatihan' => 'nullable|string|max:255',
            'program_kerja' => 'nullable|string|max:255',
            'crime_type_id' => 'required|exists:crime_types,id',
            'status' => 'required|in:aktif,dirilis,dipindahkan',
            'tanggal_masuk' => 'required|date',
            'tanggal_bebas' => 'nullable|date|after:tanggal_masuk',
        ]);

        DB::beginTransaction();
        try {
            $inmate->update($validated);

            activity()
                ->performedOn($inmate)
                ->causedBy(auth()->user())
                ->log('Data narapidana diupdate: ' . $inmate->nama);

            DB::commit();

            return redirect()->route('inmates.show', $inmate)
                ->with('success', 'Data narapidana berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating inmate: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Inmate $inmate)
    {
        // $this->authorize('delete-narapidana');

        DB::beginTransaction();
        try {
            $inmateNama = $inmate->nama;

            // Soft delete
            $inmate->delete();

            activity()
                ->performedOn($inmate)
                ->causedBy(auth()->user())
                ->log('Narapidana dihapus: ' . $inmateNama);

            DB::commit();

            return redirect()->route('inmates.index')
                ->with('success', 'Data narapidana berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting inmate: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function restore($id)
    {
        // $this->authorize('delete-narapidana');

        DB::beginTransaction();
        try {
            $inmate = Inmate::withTrashed()->findOrFail($id);
            $inmate->restore();

            activity()
                ->performedOn($inmate)
                ->causedBy(auth()->user())
                ->log('Narapidana dipulihkan: ' . $inmate->nama);

            DB::commit();

            return redirect()->route('inmates.show', $inmate)
                ->with('success', 'Data narapidana berhasil dipulihkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring inmate: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memulihkan data.');
        }
    }

    public function trashed()
    {
        // $this->authorize('delete-narapidana');

        $inmates = Inmate::onlyTrashed()
            ->with('crimeType')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('inmates.trashed', compact('inmates'));
    }
}
