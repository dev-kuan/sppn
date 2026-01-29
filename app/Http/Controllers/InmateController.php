<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInmateRequest;
use App\Http\Requests\UpdateInmateRequest;
use App\Models\Inmate;
use App\Models\CrimeType;
use App\Services\InmateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InmateController extends Controller
{
    protected $inmateService;

    public function __construct(InmateService $inmateService) {
        $this->inmateService = $inmateService;
    }

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

    public function store(StoreInmateRequest $request,)
    {
        // $this->authorize('create-narapidana');

        try {
            $inmate = $this->inmateService->storeInmate($request->validated());

            return redirect()
            ->route('inmates.show', $inmate)
            ->with('success', 'Data narapidana berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
            ->withInput()
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

    public function update(UpdateInmateRequest $request, Inmate $inmate)
    {
        // $this->authorize('edit-narapidana');
        try {
            $inmate = $this->inmateService->updateInmate($inmate, $request->validated());

            return redirect()
            ->route('inmates.show', $inmate)
            ->with('success', 'Data narapidana berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Inmate $inmate)
    {
        // $this->authorize('delete-narapidana');

        try {
            // Soft delete
            $inmate = $this->inmateService->deleteInmate($inmate);

            return redirect()
            ->route('inmates.index')
            ->with('success', 'Data narapidana dihapus.');
        } catch (\Exception $e) {
            return back()
            ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function restore($id)
    {
        // $this->authorize('delete-narapidana');
        try {
            $inmate = $this->inmateService->restoreInmate($id);

            return redirect()
            ->route('inmates.show', $inmate)
            ->with('success', 'Data narapidana berhasil dipulihkan.');
        } catch (\Exception $e) {
            return back()
            ->with('error', 'Terjadi kesalahan saat memulihkan data.');
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
