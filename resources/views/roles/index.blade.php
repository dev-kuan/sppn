<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Role & Hak Akses') }}
            </h2>
            <button @click="showModal = true; editMode = false; resetForm()"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Role
            </button>
        </div>
    </x-slot>

    <div class="py-12" x-data="roleManager()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert -->
            @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Tabel Role -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Role
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah Pengguna
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Permissions
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dibuat
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($roles as $role)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $role->name }}
                                                </div>
                                                @if($role->guard_name)
                                                <div class="text-sm text-gray-500">
                                                    Guard: {{ $role->guard_name }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $role->users_count ?? 0 }} pengguna
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($role->permissions->take(3) as $permission)
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                                    {{ $permission->name }}
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-400">Tidak ada permission</span>
                                            @endforelse
                                            @if($role->permissions->count() > 3)
                                                <span class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded font-medium">
                                                    +{{ $role->permissions->count() - 3 }} lainnya
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $role->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="editRole({{ $role->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        @if(!in_array($role->name, ['admin', 'petugas']))
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus role ini?')"
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-gray-400" title="Role default tidak dapat dihapus">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="mt-4 text-lg">Belum ada role</p>
                                        <p class="mt-2">Klik tombol "Tambah Role" untuk membuat role baru</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($roles->hasPages())
                    <div class="mt-6">
                        {{ $roles->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit Role -->
        <div x-show="showModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="showModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div x-show="showModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">

                    <form :action="editMode ? `/roles/${currentRoleId}` : '{{ route('roles.store') }}'" method="POST">
                        @csrf
                        <input x-show="editMode" type="hidden" name="_method" value="PUT">

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                        <span x-show="!editMode">Tambah Role Baru</span>
                                        <span x-show="editMode">Edit Role</span>
                                    </h3>

                                    <div class="space-y-4">
                                        <!-- Nama Role -->
                                        <div>
                                            <label for="role_name" class="block text-sm font-medium text-gray-700">
                                                Nama Role <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   id="role_name"
                                                   name="name"
                                                   x-model="formData.name"
                                                   required
                                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Permissions -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Hak Akses (Permissions)
                                            </label>
                                            <div class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto">
                                                @php
                                                    $groupedPermissions = $permissions->groupBy(function($permission) {
                                                        return explode('.', $permission->name)[0];
                                                    });
                                                @endphp

                                                @foreach($groupedPermissions as $group => $perms)
                                                <div class="mb-4 last:mb-0">
                                                    <div class="flex items-center mb-2">
                                                        <input type="checkbox"
                                                               @change="toggleGroup('{{ $group }}', $event.target.checked)"
                                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                        <label class="ml-2 text-sm font-semibold text-gray-900 uppercase">
                                                            {{ ucfirst($group) }}
                                                        </label>
                                                    </div>
                                                    <div class="ml-6 space-y-2">
                                                        @foreach($perms as $permission)
                                                        <div class="flex items-center">
                                                            <input type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $permission->id }}"
                                                                   data-group="{{ $group }}"
                                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                            <label class="ml-2 text-sm text-gray-700">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <span x-show="!editMode">Simpan</span>
                                <span x-show="editMode">Update</span>
                            </button>
                            <button type="button"
                                    @click="showModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function roleManager() {
            return {
                showModal: false,
                editMode: false,
                currentRoleId: null,
                formData: {
                    name: '',
                    permissions: []
                },

                resetForm() {
                    this.formData = {
                        name: '',
                        permissions: []
                    };
                    this.currentRoleId = null;
                    // Uncheck all checkboxes
                    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
                },

                async editRole(roleId) {
                    this.editMode = true;
                    this.currentRoleId = roleId;
                    this.showModal = true;

                    try {
                        const response = await fetch(`/roles/${roleId}/edit`);
                        const data = await response.json();

                        this.formData.name = data.role.name;

                        // Check permissions
                        setTimeout(() => {
                            data.role.permissions.forEach(permId => {
                                const checkbox = document.querySelector(`input[name="permissions[]"][value="${permId}"]`);
                                if (checkbox) checkbox.checked = true;
                            });
                        }, 100);
                    } catch (error) {
                        console.error('Error loading role:', error);
                    }
                },

                toggleGroup(group, checked) {
                    document.querySelectorAll(`input[data-group="${group}"]`).forEach(checkbox => {
                        checkbox.checked = checked;
                    });
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
