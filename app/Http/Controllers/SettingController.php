<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAspect;
use App\Models\AssessmentVariabel;
use App\Models\ObservationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class SettingController extends Controller
{

    private $configPath;

    public function __construct()
    {
        $this->configPath = config_path('institution.php');
    }
    /**
     * Display settings dashboard
     */
    public function index()
    {
        // $this->authorize('view-settings');

        $stats = [
            'variabels' => AssessmentVariabel::count(),
            'aspects' => AssessmentAspect::count(),
            'observation_items' => ObservationItem::count(),
            'active_items' => ObservationItem::aktif()->get(),
        ];

        return view('settings.index', compact('stats'));
    }

    /**
     * Manage Variabel Penilaian
     */
    public function variabels()
    {
        // $this->authorize('manage-observation-items');

        $variabels = AssessmentVariabel::withCount('aspect', 'observationItems')->get();

        return view('settings.variabels.index', compact('variabels'));
    }

    public function storeVariabel(Request $request)
    {
        // $this->authorize('manage-observation-items');

        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:assessment_variabels,nama',
        ]);

        DB::beginTransaction();
        try {
            $variabel = AssessmentVariabel::create($validated);

            activity()
                ->performedOn($variabel)
                ->causedBy(auth()->user())
                ->log('Variabel penilaian ditambahkan: ' . $variabel->nama);

            DB::commit();

            return back()->with('success', 'Variabel penilaian berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating variabel: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menambahkan variabel.');
        }
    }

    public function updateVariabel(Request $request, AssessmentVariabel $variabel)
    {
        // $this->authorize('manage-observation-items');

        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:assessment_variabel,nama,' . $variabel->id,
        ]);

        DB::beginTransaction();
        try {
            $variabel->update($validated);

            activity()
                ->performedOn($variabel)
                ->causedBy(auth()->user())
                ->log('Variabel penilaian diupdate: ' . $variabel->nama);

            DB::commit();

            return back()->with('success', 'Variabel penilaian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating variabel: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memperbarui variabel.');
        }
    }

    /**
     * Manage Aspek Penilaian
     */
    public function aspects()
    {
        // $this->authorize('manage-observation-items');

        $aspects = AssessmentAspect::with('variabel')
            ->withCount('observationItems')
            ->get();

        $variabels = AssessmentVariabel::all();

        return view('settings.aspects.index', compact('aspects', 'variabels'));
    }

    public function storeAspect(Request $request)
    {
        // $this->authorize('manage-observation-items');

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'assessment_variabel_id' => 'required|exists:assessment_variabel,id',
        ]);

        DB::beginTransaction();
        try {
            $aspect = AssessmentAspect::create($validated);

            activity()
                ->performedOn($aspect)
                ->causedBy(auth()->user())
                ->log('Aspek penilaian ditambahkan: ' . $aspect->nama);

            DB::commit();

            return back()->with('success', 'Aspek penilaian berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating aspect: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menambahkan aspek.');
        }
    }

    public function updateAspect(Request $request, AssessmentAspect $aspect)
    {
        // $this->authorize('manage-observation-items');

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'assessment_variabel_id' => 'required|exists:assessment_variabel,id',
        ]);

        DB::beginTransaction();
        try {
            $aspect->update($validated);

            activity()
                ->performedOn($aspect)
                ->causedBy(auth()->user())
                ->log('Aspek penilaian diupdate: ' . $aspect->nama);

            DB::commit();

            return back()->with('success', 'Aspek penilaian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating aspect: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memperbarui aspek.');
        }
    }

    /**
     * Manage Observation Items
     */
    public function observationItems(Request $request)
    {
        // $this->authorize('manage-observation-items');

        $query = ObservationItem::with(['variabel', 'aspect']);

        // Filter by variabel
        if ($request->has('variabel_id') && $request->variabel_id != '') {
            $query->where('variabel_id', $request->variabel_id);
        }

        // Filter by aspek
        if ($request->has('aspect_id') && $request->aspect_id != '') {
            $query->where('aspect_id', $request->aspect_id);
        }

        // Filter by status
        if ($request->has('aktif') && $request->aktif != '') {
            $query->where('aktif', $request->aktif);
        }

        $items = $query->ordered()->paginate(20)->withQueryString();

        $variabels = AssessmentVariabel::all();
        $aspects = AssessmentAspect::all();

        return view('settings.observation-items.index', compact('items', 'variabels', 'aspects'));
    }

    public function storeObservationItem(Request $request)
    {
        // $this->authorize('manage-observation-items');

        $validated = $request->validate([
            'kode' => 'required|string|max:255|unique:observation_items,kode',
            'variabel_id' => 'required|exists:assessment_variabel,id',
            'aspek_id' => 'required|exists:assessment_aspect,id',
            'nama_item' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:10',
            'jenis_frekuensi' => 'required|integer|min:0|max:31',
            'use_dynamic_frequency' => 'boolean',
            'is_conditional_weight' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'aktif' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $item = ObservationItem::create($validated);

            activity()
                ->performedOn($item)
                ->causedBy(auth()->user())
                ->log('Item observasi ditambahkan: ' . $item->nama_item);

            DB::commit();

            return back()->with('success', 'Item observasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating observation item: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menambahkan item observasi.');
        }
    }

    public function updateObservationItem(Request $request, ObservationItem $observationItem)
    {
        // $this->authorize('manage-observation-items');

        $validated = $request->validate([
            'kode' => 'required|string|max:255|unique:observation_items,kode,' . $observationItem->id,
            'variabel_id' => 'required|exists:assessment_variabel,id',
            'aspek_id' => 'required|exists:assessment_aspect,id',
            'nama_item' => 'required|string|max:255',
            'bobot' => 'required|numeric|min:0|max:10',
            'jenis_frekuensi' => 'required|integer|min:0|max:31',
            'use_dynamic_frequency' => 'boolean',
            'is_conditional_weight' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'aktif' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $observationItem->update($validated);

            activity()
                ->performedOn($observationItem)
                ->causedBy(auth()->user())
                ->log('Item observasi diupdate: ' . $observationItem->nama_item);

            DB::commit();

            return back()->with('success', 'Item observasi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating observation item: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memperbarui item observasi.');
        }
    }

    public function toggleObservationItem(ObservationItem $observationItem)
    {
        // $this->authorize('manage-observation-items');

        DB::beginTransaction();
        try {
            $observationItem->update(['aktif' => !$observationItem->aktif]);

            $status = $observationItem->aktif ? 'diaktifkan' : 'dinonaktifkan';

            activity()
                ->performedOn($observationItem)
                ->causedBy(auth()->user())
                ->log("Item observasi {$status}: " . $observationItem->nama_item);

            DB::commit();

            return back()->with('success', "Item observasi berhasil {$status}.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling observation item: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat mengubah status item.');
        }
    }

    /**
     * Backup database
     */
    public function backup()
    {
        // $this->authorize('backup-restore');

        try {
            $filename = 'backup-' . now()->format('Y-m-d_His') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // Create backups directory if not exists
            if (!Storage::exists('backups')) {
                Storage::makeDirectory('backups');
            }

            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST');

            $command = "mysqldump -u{$dbUser} -p{$dbPass} -h{$dbHost} {$dbName} > {$path}";
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('Backup failed');
            }

            activity()
                ->causedBy(auth()->user())
                ->log('Database backup dibuat: ' . $filename);

            return response()->download($path)->deleteFileAfterSend(false);
        } catch (\Exception $e) {
            Log::error('Error creating backup: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat membuat backup.');
        }
    }

    /**
     * System logs
     */
    public function logs()
    {
        // $this->authorize('view-settings');

        $activities = Activity::orderBy('created_at', 'desc')
            ->with('causer')
            ->paginate(50);

        return view('settings.logs.index', compact('activities'));
    }

    public function system() {
        return view('settings.systems.index');
    }

    public function updateSystem(Request $request) {
// Validasi input
        $validated = $request->validate([
            'institution_name' => 'required|string|max:255',
            'institution_address' => 'nullable|string|max:500',
            'institution_phone' => 'nullable|string|max:20',
            'institution_email' => 'nullable|email|max:100',

            'officer1_name' => 'required|string|max:255',
            'officer1_nip' => 'required|string|max:30',
            'officer1_position' => 'required|string|max:255',
            'officer1_signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',

            'officer2_name' => 'required|string|max:255',
            'officer2_nip' => 'required|string|max:30',
            'officer2_position' => 'required|string|max:255',
            'officer2_signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            // Load konfigurasi yang ada
            $config = $this->loadConfig();

            // Update informasi institusi
            $config['name'] = $validated['institution_name'];
            $config['address'] = $validated['institution_address'] ?? '';
            $config['phone'] = $validated['institution_phone'] ?? '';
            $config['email'] = $validated['institution_email'] ?? '';

            // Update officer 1
            $config['officers']['officer1']['name'] = $validated['officer1_name'];
            $config['officers']['officer1']['nip'] = $validated['officer1_nip'];
            $config['officers']['officer1']['position'] = $validated['officer1_position'];

            // Handle upload tanda tangan officer 1
            if ($request->hasFile('officer1_signature')) {
                // Hapus tanda tangan lama jika ada
                if (!empty($config['officers']['officer1']['signature'])) {
                    Storage::disk('public')->delete($config['officers']['officer1']['signature']);
                }

                // Simpan tanda tangan baru
                $path = $request->file('officer1_signature')->store('signatures', 'public');
                $config['officers']['officer1']['signature'] = $path;
            }

            // Update officer 2
            $config['officers']['officer2']['name'] = $validated['officer2_name'];
            $config['officers']['officer2']['nip'] = $validated['officer2_nip'];
            $config['officers']['officer2']['position'] = $validated['officer2_position'];

            // Handle upload tanda tangan officer 2
            if ($request->hasFile('officer2_signature')) {
                // Hapus tanda tangan lama jika ada
                if (!empty($config['officers']['officer2']['signature'])) {
                    Storage::disk('public')->delete($config['officers']['officer2']['signature']);
                }

                // Simpan tanda tangan baru
                $path = $request->file('officer2_signature')->store('signatures', 'public');
                $config['officers']['officer2']['signature'] = $path;
            }

            // Simpan konfigurasi ke file
            $this->saveConfig($config);

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('settings.system.index')
                ->with('success', 'Pengaturan sistem berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function loadConfig()
    {
        if (File::exists($this->configPath)) {
            return include $this->configPath;
        }

        // Return default config jika file belum ada
        return [
            'name' => 'Lembaga Pemasyarakatan',
            'address' => '',
            'phone' => '',
            'email' => '',
            'officers' => [
                'officer1' => [
                    'name' => '',
                    'nip' => '',
                    'position' => '',
                    'signature' => '',
                ],
                'officer2' => [
                    'name' => '',
                    'nip' => '',
                    'position' => '',
                    'signature' => '',
                ],
            ],
        ];
    }

    /**
     * Save configuration to file
     */
    private function saveConfig(array $config)
    {
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";

        File::put($this->configPath, $content);
    }

    /**
     * Reset settings to default
     */
    public function resetSystem()
    {
        try {
            // Hapus file signatures
            $config = $this->loadConfig();

            if (!empty($config['officers']['officer1']['signature'])) {
                Storage::disk('public')->delete($config['officers']['officer1']['signature']);
            }

            if (!empty($config['officers']['officer2']['signature'])) {
                Storage::disk('public')->delete($config['officers']['officer2']['signature']);
            }

            // Hapus file konfigurasi
            if (File::exists($this->configPath)) {
                File::delete($this->configPath);
            }

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('settings.system.index')
                ->with('success', 'Pengaturan sistem telah direset ke default.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mereset pengaturan: ' . $e->getMessage());
        }
    }
}
