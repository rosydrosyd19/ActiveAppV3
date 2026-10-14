<x-filament-panels::page.simple>
    @if (filament()->hasLogin())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/register.actions.login.before') }}

            {{ $this->loginAction }}
        </x-slot>
    @endif

    <div class="text-center mb-4">
        <h2 class="text-lg font-bold">Face Recognition Registration</h2>
        <p class="text-sm text-gray-500">Isi formulir dan arahkan wajah ke kamera</p>
    </div>

    <!-- Camera View for Registration -->
    <div wire:ignore class="relative flex justify-center items-center w-full bg-gray-200 rounded-lg overflow-hidden mb-6" style="min-height: 250px;">
        <video id="webcam" width="400" height="300" autoplay muted class="rounded-lg transform scale-x-[-1] object-cover"></video>
        
        <div id="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-50 text-white z-10">
            <svg class="animate-spin h-8 w-8 text-white mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memuat AI Model...</span>
        </div>
        
        <div id="scan-success" class="absolute inset-0 flex flex-col items-center justify-center bg-green-500 bg-opacity-80 text-white z-20 hidden">
            <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-bold text-lg">Wajah Berhasil Dipindai!</span>
        </div>

        <div id="status-text" class="text-center text-sm font-bold text-primary-600" style="position: absolute; bottom: 8px; left: 0; right: 0; background-color: rgba(255,255,255,0.8); padding: 4px 0; z-index: 20;">
            Menyiapkan kamera...
        </div>
    </div>

    <x-filament-panels::form wire:submit="register">
        <input type="hidden" wire:model="faceDescriptor" id="faceDescriptorInput">
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
            const loading = document.getElementById('loading');
            const successOverlay = document.getElementById('scan-success');
            const statusText = document.getElementById('status-text');

            const MODEL_URL = '{{ asset('models') }}';

            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);

                loading.classList.add('hidden');
                statusText.innerText = 'Mohon posisikan wajah Anda di depan kamera.';

                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;

                video.addEventListener('play', () => {
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
                                statusText.innerText = 'Wajah terdeteksi dan tersimpan! Silakan lengkapi formulir di bawah.';
                                statusText.classList.replace('text-primary-600', 'text-success-600');
                                successOverlay.classList.remove('hidden');
                                faceDetected = true;

                                const descriptorArray = Array.from(detections.descriptor);
                                
                                stream.getTracks().forEach(track => track.stop());
                                
                                // Set property directly
                                @this.set('faceDescriptor', descriptorArray);

                                // Enable submit button
                                const submitBtn = document.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                                }
                                return; // stop loop
                            }
                        } catch (e) {
                            console.error("Deteksi wajah error: ", e);
                        }
                        
                        isDetecting = false;
                        setTimeout(() => requestAnimationFrame(detectFace), 100);
                    };
                    
                    detectFace();
                });

                // Disable submit button initially
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }

            } catch (error) {
                console.error(error);
                statusText.innerText = 'Gagal mengakses kamera. Harap izinkan akses kamera.';
                statusText.classList.add('text-danger-600');
            }
        });
    </script>
</x-filament-panels::page.simple>
