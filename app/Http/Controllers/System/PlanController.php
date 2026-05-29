<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\StorePlanRequest;
use App\Http\Requests\System\UpdatePlanRequest;
use App\Http\Resources\System\PlanResource;
use App\Models\System\Plan;
use App\Services\System\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $planService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'is_free']);
        $plans = $this->planService->listPlans($filters);

        return response()->json([
            'plans' => PlanResource::collection($plans),
            'filters' => $filters,
            'stats' => [
                'total' => Plan::count(),
                'active' => Plan::where('is_active', true)->count(),
                'inactive' => Plan::where('is_active', false)->count(),
            ],
        ]);
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = $this->planService->createPlan($request->validated());

        return response()->json(['message' => 'Plan created successfully'], 201);
    }

    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        $this->planService->updatePlan($plan, $request->validated());

        return response()->json(['message' => 'Plan updated successfully']);
    }

    public function destroy(Plan $plan): JsonResponse
    {
        if ($plan->tenants()->count() > 0) {
            return response()->json(['message' => 'Cannot delete plan with active tenants'], 422);
        }

        $this->planService->deletePlan($plan);

        return response()->json(['message' => 'Plan deleted successfully']);
    }
}
