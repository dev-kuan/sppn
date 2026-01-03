<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // $this->authorize('view-users');

        $query = User::with('roles');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->byRole($request->role);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active != '') {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // $this->authorize('create-users');

        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole($validated['role']);

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('User baru ditambahkan: ' . $user->name);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan user.');
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // $this->authorize('view-users');

        $user->load('roles');

        // Get user activities
        $activities = Activity::orderBy('created_at', 'desc')
            ->causedBy($user)
            ->limit(20)
            ->get();

        // Get user statistics
        $stats = [
            'assessments_created' => $user->createdAssessments()->count(),
            'assessments_approved' => $user->approvedAssessments()->count(),
            'recommendations_made' => $user->recommendedCommitments()->count(),
        ];

        return view('users.show', compact('user', 'activities', 'stats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // $this->authorize('edit-users');

        $roles = Role::all();
        $userRole = $user->roles->first()?->name;

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // $this->authorize('edit-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'nip' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'nip' => $validated['nip'],
                'jabatan' => $validated['jabatan'],
                'is_active' => $validated['is_active'],
            ];

            // Update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Update role
            $user->syncRoles([$validated['role']]);

            activity()
                ->performedOn($user)
                ->causedBy(auth()->user())
                ->log('User diupdate: ' . $user->name);

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui user.');
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // $this->authorize('delete-users');

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::beginTransaction();
        try {
            $userName = $user->name;
            $user->delete();

            activity()
                ->causedBy(auth()->user())
                ->log('User dihapus: ' . $userName);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus user.');
        }
    }

    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = auth()->user();
        $user->load('roles');

        return view('users.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'nip' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $user->update($validated);

            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('Profile diupdate');

            DB::commit();

            return back()->with('success', 'Profile berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating profile: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui profile.');
        }
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai.');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->log('Password diubah');

            DB::commit();

            return back()->with('success', 'Password berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error changing password: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat mengubah password.');
        }
    }
}
