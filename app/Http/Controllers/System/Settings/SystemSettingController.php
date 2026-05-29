<?php

namespace App\Http\Controllers\System\Settings;

use App\Http\Controllers\Controller;
use App\Models\System\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Return the General settings data for the SPA.
     */
    public function editGeneral(): JsonResponse
    {
        return response()->json([
            'app_name' => Setting::where('key', 'app_name')->value('value') ?? config('app.name'),
            'app_logo' => Setting::where('key', 'app_logo')->value('value'),
        ]);
    }

    /**
     * Update General settings (Name & Logo).
     */
    public function updateGeneral(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|max:1024',
        ]);

        Setting::updateOrCreate(
            ['key' => 'app_name'],
            ['value' => $validated['app_name']]
        );

        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'app_logo'],
                ['value' => $path]
            );
        }

        return response()->json(['message' => 'Settings updated successfully.']);
    }

    /**
     * Return the Guest Registration setting for the SPA.
     */
    public function editGuestRegistration(): JsonResponse
    {
        $setting = Setting::where('key', 'guest_registration')->first();
        $enabled = $setting ? (bool) $setting->value : false;

        return response()->json(['guest_registration_enabled' => $enabled]);
    }

    /**
     * Update the Guest Registration setting.
     */
    public function updateGuestRegistration(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        Setting::updateOrCreate(
            ['key' => 'guest_registration'],
            ['value' => $validated['enabled']]
        );

        return response()->json(['message' => 'Settings updated successfully.']);
    }
}
