<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}

            {{ $this->registerAction }}
        </x-slot>
    @endif

    <div class="text-center mb-4">
        <h2 class="text-lg font-bold">Face Recognition Login</h2>
        <p class="text-sm text-gray-500">Silakan menghadap ke kamera</p>
    </div>

    <div wire:ignore class="relative flex justify-center items-center w-full bg-gray-200 rounded-lg overflow-hidden" style="min-height: 250px;">
        <!-- Video element for webcam -->
        <video id="webcam" width="400" height="300" autoplay muted class="rounded-lg transform scale-x-[-1] object-cover"></video>
        <!-- Overlay canvas for drawing detections -->
        <canvas id="overlay" class="absolute top-0 left-0 w-full h-full transform scale-x-[-1]"></canvas>
        
        <div id="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-50 text-white z-10">
            <svg class="animate-spin h-8 w-8 text-white mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memuat AI Model...</span>
        </div>

        <div id="status-text" class="text-center text-sm font-bold text-primary-600" style="position: absolute; bottom: 8px; left: 0; right: 0; background-color: rgba(255,255,255,0.8); padding: 4px 0; z-index: 20;">
            Menyiapkan kamera...
        </div>
    </div>

    <div class="my-6 flex items-center justify-center">
        <div class="border-t border-gray-300 flex-grow"></div>
        <span class="px-4 text-sm text-gray-500 font-medium">ATAU</span>
        <div class="border-t border-gray-300 flex-grow"></div>
    </div>

    <!-- The standard login form -->
    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}
        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <div class="mt-4">
        @if ($errors->any())
            <div class="text-danger-600 text-center text-sm font-bold">
                {{ $errors->first() }}
            </div>
        @endif
    </div>

    <!-- Import face-api.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const video = document.getElementById('webcam');
            const overlay = document.getElementById('overlay');
            const loading = document.getElementById('loading');
            const statusText = document.getElementById('status-text');

            const MODEL_URL = '{{ asset('models') }}';

            try {
                // Load Models
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);

                loading.classList.add('hidden');
                statusText.innerText = 'Mohon izinkan akses kamera Anda.';

                // Start Webcam
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;

                video.addEventListener('play', () => {
                    const canvas = faceapi.createCanvasFromMedia(video);
                    // Match dimensions
                    const displaySize = { width: video.width, height: video.height };
                    faceapi.matchDimensions(canvas, displaySize);
                    
                    statusText.innerText = 'Mencari wajah Anda...';
                    
                    let faceDetected = false;
                    let isDetecting = false;

                    const detectFace = async () => {
                        if (faceDetected) return;
                        if (isDetecting) {
                            requestAnimationFrame(detectFace);
                            return;
                        }
                        
                        isDetecting = true;
                        
                        try {
                            const detections = await faceapi.detectSingleFace(
                                video, 
                                new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.5 })
                            )
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                            if (detections) {
                                statusText.innerText = 'Wajah terdeteksi! Memproses login...';
                                statusText.classList.replace('text-primary-600', 'text-success-600');
                                faceDetected = true;

                                // Send descriptor to Livewire backend
                                const descriptorArray = Array.from(detections.descriptor);
                                
                                // Stop camera stream
                                stream.getTracks().forEach(track => track.stop());
                                
                                // Call livewire method
                                @this.loginWithFace(descriptorArray);
                                return; // Stop the loop
                            }
                        } catch (e) {
                            console.error("Deteksi wajah error: ", e);
                        }

                        isDetecting = false;
                        
                        // Repeat detection loop
                        setTimeout(() => requestAnimationFrame(detectFace), 100);
                    };

                    // Start detection loop
                    detectFace();
                });

            } catch (error) {
                console.error(error);
                statusText.innerText = 'Gagal mengakses kamera atau memuat model AI.';
                statusText.classList.add('text-danger-600');
            }
        });
    </script>
</x-filament-panels::page.simple>
