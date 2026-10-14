<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use App\Models\SystemAuthFaceData;
use Illuminate\Database\Eloquent\Model;

class FaceRegister extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.face-register';

    public $faceDescriptor = null;

    public function registerFace($descriptor)
    {
        $this->faceDescriptor = $descriptor;
        // The frontend will set this when a face is captured
    }

    protected function handleRegistration(array $data): Model
    {
        if (is_string($this->faceDescriptor)) {
            $this->faceDescriptor = json_decode($this->faceDescriptor, true);
        }

        \Illuminate\Support\Facades\Log::info('Face descriptor on submit: ', ['descriptor' => $this->faceDescriptor]);

        // Check if face is captured
        if (!$this->faceDescriptor || count($this->faceDescriptor) !== 128) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Wajah belum dipindai! Silakan hadap ke kamera hingga terdeteksi. ' . (is_array($this->faceDescriptor) ? 'Data count: ' . count($this->faceDescriptor) : 'Data is null'),
            ]);
        }

        // Check for duplicate face
        $allFaces = SystemAuthFaceData::all();
        foreach ($allFaces as $faceData) {
            $dbDescriptor = $faceData->descriptor;
            if (is_string($dbDescriptor)) {
                $dbDescriptor = json_decode($dbDescriptor, true);
            }

            if (!is_array($dbDescriptor) || count($dbDescriptor) !== 128) continue;

            $distance = $this->euclideanDistance($this->faceDescriptor, $dbDescriptor);

            if ($distance < 0.65) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'Wajah ini sudah terdaftar pada sistem! Anda tidak bisa membuat akun ganda. Silakan gunakan fitur Login.',
                ]);
            }
        }

        // Base registration creates the user
        $user = parent::handleRegistration($data);

        // Save face data
        SystemAuthFaceData::create([
            'user_id' => $user->id,
            'descriptor' => $this->faceDescriptor,
        ]);

        return $user;
    }

    private function euclideanDistance($a, $b)
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }
}
