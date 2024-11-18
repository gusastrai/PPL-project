<x-app-guru-layout>
    <div class="px-3 py-5 mx-4 my-6 bg-white rounded-lg shadow xl:p">
        {{-- Breadcrumb --}}
        @php
            $breadcrumbs = [
                ['label' => 'Dashboard', 'route' => route('guru.dashboard')],
                ['label' => 'LMS', 'route' => route('guru.dashboard.lms')],
                ['label' => 'Buat tugas ' . $mataPelajaran->nama_matpel . ' ' . $kelas->nama_kelas],
            ];
        @endphp

        <x-breadcrumb :breadcrumbs="$breadcrumbs" />
          @if (session()->has('success'))
                <x-alert-notification :color="'blue'">
                    {{ session('success') }}
                </x-alert-notification>
            @endif

        <form action="{{ route('guru.dashboard.lms.tugas.store', $id) }}" method="POST" enctype="multipart/form-data"
            class="mt-6">
            @csrf
            <input type="hidden" name="kelas_mata_pelajaran_id" value="{{ $id }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Content Column (full width on mobile, 2/3 on desktop) -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Judul -->
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Materi</label>
                        <input type="text" name="judul" id="judul"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Materi
                            (Optional)</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">File Materi</label>
                        <div
                            class="mt-1 flex justify-center px-4 py-4 md:px-6 md:pt-5 md:pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <div class="flex flex-col items-center">
                                    <label for="files"
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:underline">
                                        <span>Upload files</span>
                                        <input id="files" name="files[]" type="file" class="sr-only" multiple
                                            accept=".pdf,.doc,.docx,.ppt,.pptx">
                                    </label>
                                    <p class="text-xs mt-4 text-gray-500">PDF, DOC, DOCX, PPT, PPTX up to 10MB each</p>
                                </div>
                                <div id="file-list" class="mt-4 text-sm text-gray-500"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (full width on mobile, 1/3 on desktop) -->
                <div class="space-y-6">
                    <!-- Tenggat -->
                    <div>
                        <label for="tenggat" class="block text-sm font-medium text-gray-700">Tenggat</label>
                        <input type="datetime-local" name="tenggat" id="tenggat"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Topik -->
                    <div>
                        <label for="topik_id" class="block text-sm font-medium text-gray-700">Topik</label>
                        <select name="topik_id" id="topik_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Pilih Topik</option>
                            @foreach ($topiks as $topik)
                                <option value="{{ $topik->id_topik }}">{{ $topik->judul_topik }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-y-3">
                        <button type="button"  onclick="openModal()"
                            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-500 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Tambah Topik
                        </button>
                        <button type="submit"
                            class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Tugaskan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- modal tambah topik --}}
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
                            <input type="hidden" name="dari_tugas" value="1">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedFiles = [];
            const fileInput = document.getElementById('files');
            const fileList = document.getElementById('file-list');

            fileInput.addEventListener('change', function(e) {
                const newFiles = Array.from(this.files);
                selectedFiles = [...selectedFiles, ...
                    newFiles
                ]; // Gabungkan file baru dengan file sebelumnya
                updateFileList();
            });

            function updateFileList() {
                fileList.innerHTML = ''; // Bersihkan daftar file sebelumnya
                selectedFiles.forEach((file, index) => {
                    const fileItem = `
                        <div class="flex gap-4 items-center justify-between underline">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">${file.name}</span>
                            </div>
                            <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    fileList.insertAdjacentHTML('beforeend', fileItem);
                });
            }

            window.removeFile = function(index) {
                selectedFiles.splice(index, 1); // Hapus file dari array
                updateFileList(); // Perbarui tampilan
            };

            // Tangani pengiriman form
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                // Tambahkan semua file dari selectedFiles ke FormData
                selectedFiles.forEach((file) => {
                    formData.append('files[]', file);
                });

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            window.location.href = data.redirect || '/dashboard';
                        }
                    })
                    .catch((error) => console.error('Error:', error));
            });
        });



        // Modal Topik
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

    </script>
</x-app-guru-layout>