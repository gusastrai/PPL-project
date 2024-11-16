<x-app-guru-layout>
    <div class="px-3 py-5 mx-4 my-6 bg-white rounded-lg shadow xl:p-6">
        {{-- Breadcrumb --}}
        @php
            $breadcrumbs = [
                ['label' => 'Dashboard', 'route' => route('guru.dashboard')],
                ['label' => 'LMS', 'route' => route('guru.dashboard.lms')],
                ['label' => $mataPelajaran->nama_matpel . ' ' . $kelas->nama_kelas],
            ];
        @endphp

        <x-breadcrumb :breadcrumbs="$breadcrumbs" />

        <div class="px-3">
            {{-- Tabs --}}
            <div class="flex gap-2 mb-4 mt-6 overflow-x-auto whitespace-nowrap">
                <x-nav-button-lms route="guru.dashboard.lms.forum" :id="$id" label="Forum" />
                <x-nav-button-lms route="guru.dashboard.lms.forum.tugas" :id="$id" label="Tugas" />
                <x-nav-button-lms route="guru.dashboard.lms.forum.anggota" :id="$id" label="Anggota" />
                <x-nav-button-lms route="guru.dashboard.lms.forum.nilai_kelas" :id="$id" label="Nilai" />
            </div>
            @if (session()->has('success'))
                <x-alert-notification :color="'blue'">
                    {{ session('success') }}
                </x-alert-notification>
            @endif
            {{-- Tombol Buat --}}
            <div class="flex items-center justify-between mt-8 mb-4 max-w-3xl mx-auto">
                <h2 class="text-lg font-semibold">Daftar Bab</h2>
                <div class="relative inline-block text-left">
                    <button onclick="toggleDropdown()" class="px-4 py-2 text-white bg-blue-500 rounded-full">
                        + Buat
                    </button>
                    <div id="dropdown-menu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg">
                        <a href="" class="flex items-center px-4 py-2 text-gray-800 hover:bg-gray-100">
                            Tugas
                        </a>
                        <a href="" class="flex items-center px-4 py-2 text-gray-800 hover:bg-gray-100">
                            Materi
                        </a>
                        <hr class="border-gray-200">
                        <button class="flex items-center px-4 py-2 text-gray-800 hover:bg-gray-100 w-full"
                            onclick="openModal()">
                            Topik
                        </button>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="flex flex-col items-center">
                @foreach ($listTopik as $topik)
                    <div class="w-full max-w-3xl">
                        {{-- Topic Title --}}
                        <div class="flex justify-between items-center mt-6">
                            <h2 class="text-lg font-semibold">{{ $topik->judul_topik }}</h2>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <svg width="24" height="24" fill="currentColor">
                                        <circle cx="12" cy="5" r="2"></circle>
                                        <circle cx="12" cy="12" r="2"></circle>
                                        <circle cx="12" cy="19" r="2"></circle>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                        <button type="button"
                                            @click="$dispatch('open-modal', 'edit-topic-{{ $topik->id_topik }}')"
                                            class="w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100">
                                            Edit Topik
                                        </button>
                                        <form action="{{ route('guru.dashboard.lms.topik.destroy', $topik->id_topik) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus topik ini?')"
                                            class="block w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full px-4 py-2 text-left text-red-600 hover:bg-gray-100">
                                                Hapus Topik
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal for each topic -->
                        <div x-data="{ showModal: false }" x-show="showModal"
                            x-on:open-modal.window="showModal = ($event.detail === 'edit-topic-{{ $topik->id_topik }}')"
                            x-on:keydown.escape.window="showModal = false" class="fixed inset-0 z-50"
                            style="display: none;">
                            <div class="fixed inset-0 bg-black bg-opacity-50"></div>
                            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto"
                                    @click.away="showModal = false">
                                    <form action="{{ route('guru.dashboard.lms.topik.update', $topik->id_topik) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="px-6 py-4 border-b">
                                            <h3 class="text-lg font-medium text-gray-900">Edit Topik</h3>
                                        </div>
                                        <div class="px-6 py-4">
                                            <div class="relative">
                                                <input type="text" name="topic" value="{{ $topik->judul_topik }}"
                                                    maxlength="200"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="px-6 py-4 border-t flex justify-end space-x-2">
                                            <button type="button" @click="showModal = false"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @forelse ($topik->tugas as $tugas)
                            <a href="{{ route('guru.dashboard.lms.detail.tugas', $tugas->id_tugas) }}"
                                class="block hover:bg-gray-200">
                                <div class="p-4 mt-4 border rounded-lg border-black">
                                    <div class="flex items-center">
                                        <svg width="28" height="36" viewBox="0 0 28 36" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M1.05254 6.87192C0.583496 8.31548 0.583496 10.0992 0.583496 13.6667V27.8258C0.583496 31.5003 0.583496 33.3375 1.23891 34.3092C2.0396 35.4962 3.43508 36.138 4.8574 35.9734C6.02166 35.8387 7.41659 34.6431 10.2064 32.2518C11.4345 31.1991 12.0486 30.6728 12.7231 30.4345C13.5494 30.1425 14.4509 30.1425 15.2773 30.4345C15.9517 30.6728 16.5658 31.1991 17.7938 32.2517C20.5837 34.643 21.9787 35.8387 23.1429 35.9734C24.5652 36.138 25.9607 35.4962 26.7614 34.3092C27.4168 33.3375 27.4168 31.5003 27.4168 27.8258V13.6667C27.4168 10.0992 27.4168 8.31548 26.9478 6.87192C25.9998 3.95439 23.7124 1.667 20.7949 0.719042C19.3513 0.25 17.5676 0.25 14.0002 0.25C10.4327 0.25 8.64898 0.25 7.20542 0.719042C4.28789 1.667 2.0005 3.95439 1.05254 6.87192ZM8.25016 10.3125C7.45625 10.3125 6.81266 10.9561 6.81266 11.75C6.81266 12.5439 7.45625 13.1875 8.25016 13.1875H19.7502C20.5441 13.1875 21.1877 12.5439 21.1877 11.75C21.1877 10.9561 20.5441 10.3125 19.7502 10.3125H8.25016Z"
                                                fill="#2D264B" />
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-base font-semibold">{{ $tugas->judul }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ $tugas->created_at ? \Carbon\Carbon::parse($tugas->created_at)->format('d F Y') : '' }}
                                                | Tenggat:
                                                {{ $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline)->format('d F Y, H:i') : 'Tidak ada tenggat' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="text-gray-500 text-center font-semibold py-4">Tidak ada tugas untuk topik ini.</p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal tambah topik -->
    <div id="topicModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto" onclick="event.stopPropagation()">
                <form action="{{ route('guru.dashboard.lms.topik.store', $id) }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-lg font-medium text-gray-900">
                            Tambahkan topik
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="relative">
                            <input type="hidden" name="mata_pelajaran_id" value="{{ $mataPelajaran->id_matpel }}">
                            <input type="hidden" name="kelas_mata_pelajaran_id" value="{{ $id }}">
                            <input type="text" name="topic" id="topicInput" maxlength="200"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Topik" oninput="updateCharCount(this)" required>
                        </div>
                        <div class="text-right text-sm text-gray-500 mt-1">
                            <span id="charCount">0</span>/200
                        </div>
                        @error('topic')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="px-6 py-4 border-t flex justify-end space-x-2">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Tambahkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        // Dropdown Toggle
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown-menu');
            dropdown.classList.toggle('hidden');
        }

        // Add Topic Modal
        function openModal() {
            document.getElementById('topicModal').classList.remove('hidden');
            document.getElementById('topicInput').focus();
        }

        function closeModal() {
            const modal = document.getElementById('topicModal');
            const input = document.getElementById('topicInput');
            const charCount = document.getElementById('charCount');

            modal.classList.add('hidden');
            input.value = '';
            charCount.textContent = '0';
        }

        function updateCharCount(input) {
            document.getElementById('charCount').textContent = input.value.length;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown-menu');
            const dropdownButton = document.querySelector('[onclick="toggleDropdown()"]');

            if (!dropdown.contains(event.target) && !dropdownButton.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</x-app-guru-layout>