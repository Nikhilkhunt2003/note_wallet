<div
    x-data="{
        fontSize: parseInt(localStorage.getItem('notepad_fontSize')) || 16,
        note: $wire.entangle('note'),
        saveTimeout: null,

    /* Modal State */
    showEditUrlModal: false,
    newUrl: '',
    urlError: '',
    isCheckingUrl: false, /* New: Tracks loading state */

    /* Modal Functions */
    openEditUrlModal() {
        this.newUrl = this.$wire.url;
        this.urlError = '';
        this.isCheckingUrl = false;
        this.showEditUrlModal = true;
    },
    closeEditSlugModal() {
        this.showEditUrlModal = false;
        this.newUrl = '';
        this.urlError = '';
        this.isCheckingUrl = false;
    },

    /* jQuery AJAX Validation Function */
   checkSlugExists(url) {
            const params = new URLSearchParams({
                url: url,
                current_url: this.$wire.url
            });

            return fetch(`/check-url?${params}`)
                .then(response => response.json())
                .then(data => data.exists)
                .catch(error => {
                    console.error('Error checking url:', error);
                    return false;
                });
        },

        saveSlug() {
            this.urlError = '';
            const formattedSlug = this.newUrl.trim();

            if (!formattedSlug) {
                this.urlError = 'Slug cannot be empty.';
                return;
            }
            if (!/^[a-zA-Z0-9]+$/.test(formattedSlug)) {
                this.urlError = 'Only letters and numbers allowed.';
                return;
            }

            this.isCheckingUrl = true;

            this.checkSlugExists(formattedSlug).then(exists => {
                this.isCheckingUrl = false;

                if (exists) {
                    this.urlError = 'This URL is already taken. Please choose another.';
                    return;
                }

                this.$wire.saveNewSlug(formattedSlug);
            }).catch(() => {
                this.isCheckingUrl = false;
                this.urlError = 'An error occurred while checking the url.';
            });
        },

        /* Debounced save function - waits 500ms after typing stops */
        debouncedSave() {
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => {
                $wire.set('note', this.note);
            }, 500);
        },

        increaseFont() {
            if (this.fontSize < 72) {
                this.fontSize += 2;
                localStorage.setItem('notepad_fontSize', this.fontSize);
            }
        },
        decreaseFont() {
            if (this.fontSize > 8) {
                this.fontSize -= 2;
                localStorage.setItem('notepad_fontSize', this.fontSize);
            }
        }
    }"
    class="min-h-screen bg-notepad-bg flex flex-col"
>
    <!-- Main Container -->
    <div class="flex-1 max-w-7xl mx-auto w-full p-4">
        <!-- Notepad Card -->
        <div class="bg-notepad-white rounded shadow-sm border border-gray-200 flex flex-col h-[calc(100vh-120px)]">

            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                    </svg>
                    <span class="text-xl font-semibold text-gray-400">notepad</span>
                </div>

                <!-- Toolbar Icons -->
                <div class="flex items-center gap-4">
                    <a href="{{url('/')}}" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </a>
                    <button class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </button>
                    <button @click="openEditUrlModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </button>
                    <span class="text-gray-400 text-sm">SP</span>
                    <span class="text-gray-400 text-sm">MO</span>
                    <button class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </button>
                    <button class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                    <button
                        class="bg-pink-200 hover:bg-pink-300 text-pink-700 px-3 py-1 rounded-full text-xs font-medium transition">
                        Remove Ads
                    </button>
                </div>
            </div>

            <!-- Text Area -->
            <div class="flex-1 relative overflow-hidden">
        <textarea
            x-model="note"
            @input="debouncedSave()"
            :style="'font-size: ' + fontSize + 'px'"
            class="w-full h-full p-4 resize-none outline-none text-gray-700 leading-relaxed"
            placeholder="Start typing..."
        ></textarea>
            </div>

            <!-- Bottom Toolbar -->
            <div class="border-t border-gray-200 px-4 py-2">
                <div class="flex items-center justify-between">
                    <!-- Left Side - Font Controls -->
                    <div class="flex items-center gap-2">
                        <button
                            @click="decreaseFont()"
                            class="w-7 h-7 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition text-sm"
                            title="Decrease font size"
                        >
                            −
                        </button>
                        <button
                            @click="increaseFont()"
                            class="w-7 h-7 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition text-sm"
                            title="Increase font size"
                        >
                            +
                        </button>
                        <span class="text-xs text-gray-400 ml-2" x-text="fontSize + 'px'"></span>
                    </div>

                    <!-- Right Side - Mode Buttons -->
                    <div class="flex items-center gap-2">
                        <button
                            class="px-2 py-1 text-xs rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition">
                            Raw
                        </button>
                        <button
                            class="px-2 py-1 text-xs rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition">
                            Markdown
                        </button>
                        <button
                            class="px-2 py-1 text-xs rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition">
                            Code
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Link Buttons -->
        <div class="flex justify-center gap-3 mt-4">
            <button
                class="flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded text-sm text-gray-500 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Editable Link
            </button>
            <button
                class="flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded text-sm text-gray-500 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                Share Link
            </button>
        </div>

        <!-- Word Count -->
        <div class="text-center mt-2 text-xs text-gray-400">
            Words: <span x-text="note.trim() === '' ? 0 : note.trim().split(/\s+/).length"></span> |
            Chars: <span x-text="note.length"></span>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-auto py-4 text-center">
        <div class="flex justify-center gap-2 text-sm text-gray-400">
            <a href="#" class="hover:text-gray-600 transition">Privacy</a>
            <span>−</span>
            <a href="#" class="hover:text-gray-600 transition">Terms</a>
            <span>−</span>
            <a href="#" class="hover:text-gray-600 transition">Contact Us</a>
            <span>−</span>
            <a href="#" class="hover:text-gray-600 transition">About Us</a>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- EDIT SLUG MODAL                            -->
    <!-- ========================================== -->
    <div
        x-show="showEditUrlModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-cloak
    >
        <div
            x-show="showEditUrlModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100"
            @click.outside="closeEditSlugModal()"
        >
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-semibold text-gray-800">Edit Note Slug</h3>
                <button @click="closeEditSlugModal()"
                        class="text-gray-400 hover:text-gray-600 transition p-1 rounded-full hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Custom URL Slug</label>
                <div class="flex items-center gap-2">
                    <span class="text-gray-400 text-sm whitespace-nowrap">{{url('')}}/</span>
                    <input
                        type="text"
                        x-model="newUrl"
                        class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-gray-800 tracking-wider font-medium"
                        placeholder="ABC123"
                        maxlength="20"
                        @keydown.enter="saveSlug()"
                        autofocus
                    >
                </div>
                <p class="mt-2 text-xs text-gray-500">Use only uppercase letters and numbers.</p>

                <!-- Error Message -->
                <div x-show="urlError" x-transition
                     class="mt-3 p-3 bg-red-50 border border-red-100 rounded-lg flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p x-text="urlError" class="text-sm text-red-600"></p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                <button
                    @click="closeEditSlugModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm"
                >
                    Cancel
                </button>

                <!-- Updated Save Button with Loading State -->
                <button
                    @click="saveSlug()"
                    :disabled="isCheckingUrl"
                    class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition shadow-sm disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2"
                >
                    <!-- Loading Spinner (Only shows when isCheckingUrl is true) -->
                    <svg x-show="isCheckingUrl" class="animate-spin h-4 w-4 text-white"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span x-text="isCheckingUrl ? 'Checking...' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
