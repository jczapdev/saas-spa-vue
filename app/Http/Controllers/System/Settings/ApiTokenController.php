<?php

namespace App\Http\Controllers\System\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    /**
     * Generate a new API token for the authenticated user.
     */
    public function generate(Request $request)
    {
        // Revoke all existing tokens
        $request->user()->tokens()->delete();

        // Create new token
        $token = $request->user()->createToken('api-token');

        return redirect()->back()->with([
            'success' => 'API token generated successfully.',
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * Revoke all API tokens for the authenticated user.
     */
    public function revoke(Request $request)
    {
        $request->user()->tokens()->delete();

        return redirect()->back()->with('success', 'API token revoked successfully.');
    }
}
