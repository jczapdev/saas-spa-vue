<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\System\Plan;
use App\Models\System\Setting;
use App\Services\System\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class GuestRegisterController extends Controller
{
    public function __construct(protected TenantService $tenantService) {}

    public function index(): JsonResponse
    {
        $setting = Setting::where('key', 'guest_registration')->first();
        $enabled = $setting ? (bool) $setting->value : false;

        if (! $enabled) {
            return response()->json(['enabled' => false], 403);
        }

        $freePlan = Plan::where('price', 0)->first();

        return response()->json([
            'enabled' => true,
            'app_url_base' => config('app.url_base'),
            'free_plan' => $freePlan ? [
                'id' => $freePlan->id,
                'name' => $freePlan->name,
                'description' => $freePlan->description,
            ] : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $setting = Setting::where('key', 'guest_registration')->first();
        if (! ($setting ? (bool) $setting->value : false)) {
            return response()->json(['message' => 'Registration is disabled.'], 403);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255', 'unique:tenants,name'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'string', 'email', 'max:255'],
            'domain' => ['required', 'string', 'max:63', 'alpha_dash', 'unique:domains,domain'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $freePlan = Plan::where('price', 0)->first();

        try {
            $data = [
                'name' => $validated['company_name'],
                'owner_name' => $validated['owner_name'],
                'owner_email' => $validated['owner_email'],
                'owner_password' => $validated['password'],
                'domain' => $validated['domain'],
                'plan_id' => $freePlan?->id,
                'status' => 'Active',
            ];

            $tenant = $this->tenantService->createTenant($data);

            $protocol = request()->secure() ? 'https://' : 'http://';
            $tenantDomain = $tenant->domains->first()->domain;
            $redirectUrl = $protocol.$tenantDomain.'/login';

            return response()->json(['redirect_url' => $redirectUrl]);

        } catch (\Exception $e) {
            Log::error('Guest registration failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
