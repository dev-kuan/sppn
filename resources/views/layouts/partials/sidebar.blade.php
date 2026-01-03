
<!-- Logo -->
<div class="flex h-16 shrink-0 items-center">
    <img class="h-10 w-auto" src="/images/logo.png" alt="SPPN">
    <span class="ml-3 text-xl font-bold text-gray-900">SPPN</span>
</div>

<!-- Navigation -->
<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'bg-gray-100 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- Narapidana -->
                @can('view-narapidana')
                <li>
                    <a href="{{ route('inmates.index') }}"
                       class="{{ request()->routeIs('inmates.*') ? 'bg-gray-100 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Data Narapidana
                    </a>
                </li>
                @endcan

                <!-- Penilaian -->
                @can('view-penilaian')
                <li>
                    <a href="{{ route('assessments.index') }}"
                       class="{{ request()->routeIs('assessments.*') ? 'bg-gray-100 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                        Penilaian
                    </a>
                </li>
                @endcan

                <!-- Laporan -->
                @can('view-laporan')
                <li>
                    <a href="{{ route('reports.index') }}"
                       class="{{ request()->routeIs('reports.*') ? 'bg-gray-100 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Laporan
                    </a>
                </li>
                @endcan

                <!-- Manajemen User -->
                @can('view-users')
                <li>
                    <a href="{{ route('users.index') }}"
                       class="{{ request()->routeIs('users.*') ? 'bg-gray-100 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Manajemen User
                    </a>
                </li>
                @endcan

                <!-- Pengaturan -->
                @can('view-settings')
                <li x-data="{ open: {{ request()->routeIs('settings.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" type="button"
                            class="w-full text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                        <svg class="ml-auto h-5 w-5 transition-transform" :class="{ 'rotate-90': open }" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                        <li>
                            <a href="{{ route('settings.variabels') }}"
                               class="{{ request()->routeIs('settings.variabels') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 pl-9 text-sm leading-6">
                                Variabel Penilaian
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.aspects') }}"
                               class="{{ request()->routeIs('settings.aspects') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 pl-9 text-sm leading-6">
                                Aspek Penilaian
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.observation-items') }}"
                               class="{{ request()->routeIs('settings.observation-items') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 pl-9 text-sm leading-6">
                                Item Observasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('settings.logs') }}"
                               class="{{ request()->routeIs('settings.logs') ? 'bg-gray-100 text-indigo-600' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 pl-9 text-sm leading-6">
                                Activity Log
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
            </ul>
        </li>

        <!-- User Info -->
        <li class="-mx-6 mt-auto">
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50">
                <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="sr-only">Your profile</span>
                <span aria-hidden="true">
                    <span class="block text-xs font-semibold">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-gray-500">{{ auth()->user()->role_name }}</span>
                </span>
            </a>
        </li>
    </ul>
</nav>
