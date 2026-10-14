<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemAuthFaceData;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireToast\Facades\Toast;

class FaceLogin extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.face-login';

    public function loginWithFace($descriptor)
    {
        // $descriptor is an array of 128 floats from face-api.js
        if (!is_array($descriptor) || count($descriptor) !== 128) {
            $this->addError('email', 'Invalid face data received.');
            return;
        }

        // Fetch all face data
        $allFaces = SystemAuthFaceData::with('user')->get();
        $bestMatch = null;
        $lowestDistance = 1.0;

        foreach ($allFaces as $faceData) {
            $dbDescriptor = $faceData->descriptor;
            if (is_string($dbDescriptor)) {
                $dbDescriptor = json_decode($dbDescriptor, true);
            }

            if (!is_array($dbDescriptor) || count($dbDescriptor) !== 128) continue;

            $distance = $this->euclideanDistance($descriptor, $dbDescriptor);

            if ($distance < $lowestDistance) {
                $lowestDistance = $distance;
                $bestMatch = $faceData;
            }
        }

        // Threshold for face match, typically 0.6 for face-api.js
        if ($bestMatch && $lowestDistance < 0.65) {
            Auth::login($bestMatch->user);
            session()->regenerate();
            return redirect()->intended(filament()->getCurrentPanel()->getUrl());
        }

        // If no match found
        throw ValidationException::withMessages([
            'email' => 'Wajah tidak dikenali atau belum terdaftar.',
        ]);
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
