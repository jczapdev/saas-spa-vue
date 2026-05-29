<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\StoreTenantRequest;
use App\Http\Requests\System\UpdateTenantRequest;
use App\Http\Resources\System\TenantResource;
use App\Models\System\Plan;
use App\Models\System\Tenant;
use App\Services\System\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'plan_id', 'is_active']);

        $tenants = $this->tenantService->listTenants($filters);

        return response()->json([
            'tenants' => TenantResource::collection($tenants),
            'filters' => $filters,
            'stats' => [
                'total' => Tenant::count(),
                'active' => Tenant::where('status', 'Active')->count(),
                'trial' => Tenant::where('status', 'Trial')->count(),
                'canceled' => Tenant::where('status', 'Canceled')->count(),
            ],
            'plans' => Plan::where('is_active', true)->get()->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price_formatted' => $plan->currency.' '.number_format($plan->price, 2),
                ];
            }),
        ]);
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();

        $tenant = $this->tenantService->createTenant($validated);

        return response()->json(['message' => 'Tenant created successfully! Database and domain configured.'], 201);
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $tenant = $this->tenantService->updateTenant($tenant, $request->validated());

        return response()->json(['message' => 'Tenant updated successfully!']);
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(Tenant $tenant)
    {
        try {
            $this->tenantService->deleteTenant($tenant);

            return response()->json(['message' => 'Tenant deleted successfully! Database and domains removed.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancela un tenant y comienza el período de gracia de 30 días.
     */
    public function cancel(Tenant $tenant)
    {
        $tenant = $this->tenantService->cancelTenant($tenant);

        return response()->json(['message' => 'Tenant canceled successfully. Grace period started.']);
    }

    /**
     * Restaura un tenant cancelado (si está dentro del período de gracia).
     */
    public function restore(Tenant $tenant)
    {
        try {
            $tenant = $this->tenantService->restoreTenant($tenant);

            return response()->json(['message' => 'Tenant restored successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
