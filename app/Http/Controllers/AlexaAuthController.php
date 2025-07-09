<?php

namespace App\Http\Controllers;

use App\Services\SmartHomeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlexaAuthController extends Controller
{
    /**
     * The smart home service instance.
     *
     * @var SmartHomeService
     */
    protected $smartHomeService;

    /**
     * Create a new controller instance.
     *
     * @param SmartHomeService $smartHomeService
     * @return void
     */
    public function __construct(SmartHomeService $smartHomeService)
    {
        $this->smartHomeService = $smartHomeService;
    }

    /**
     * Redirect the user to the Amazon authorization page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToAmazon()
    {
        $state = Str::random(40);
        session(['alexa_auth_state' => $state]);

        $query = http_build_query([
            'client_id' => config('services.alexa.client_id'),
            'redirect_uri' => config('services.alexa.redirect'),
            'response_type' => 'code',
            'scope' => 'alexa:skills:read',
            'state' => $state,
        ]);

        return redirect('https://www.amazon.com/ap/oa?' . $query);
    }

    /**
     * Handle the callback from Amazon.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleAmazonCallback(Request $request)
    {
        // Verify state to prevent CSRF attacks
        if ($request->state !== session('alexa_auth_state')) {
            return redirect()->route('smart-devices.index')
                ->with('error', 'Invalid state parameter. Authorization failed.');
        }

        // Check for error response
        if ($request->has('error')) {
            Log::error('Amazon OAuth error: ' . $request->error_description);
            return redirect()->route('smart-devices.index')
                ->with('error', 'Authorization failed: ' . $request->error_description);
        }

        // Exchange authorization code for access token
        try {
            $response = Http::post('https://api.amazon.com/auth/o2/token', [
                'grant_type' => 'authorization_code',
                'code' => $request->code,
                'client_id' => config('services.alexa.client_id'),
                'client_secret' => config('services.alexa.client_secret'),
                'redirect_uri' => config('services.alexa.redirect'),
            ]);

            if (!$response->successful()) {
                Log::error('Failed to get access token: ' . $response->body());
                return redirect()->route('smart-devices.index')
                    ->with('error', 'Failed to get access token from Amazon.');
            }

            $tokenData = $response->json();

            // Create or update the platform in the database
            $platformData = [
                'name' => 'Amazon Alexa',
                'slug' => 'alexa',
                'description' => 'Amazon Alexa Smart Home Skill',
                'is_active' => true,
                'credentials' => [
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'] ?? null,
                    'expires_in' => $tokenData['expires_in'] ?? 3600,
                    'token_type' => $tokenData['token_type'] ?? 'bearer',
                ],
            ];

            // Check if platform already exists
            $existingPlatform = $this->smartHomeService->getPlatformBySlug('alexa');

            if ($existingPlatform) {
                $this->smartHomeService->updatePlatform($existingPlatform->id, $platformData);
                $message = 'Amazon Alexa account reconnected successfully.';
            } else {
                $this->smartHomeService->createPlatform($platformData);
                $message = 'Amazon Alexa account connected successfully.';
            }

            return redirect()->route('smart-devices.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Exception during Amazon OAuth: ' . $e->getMessage());
            return redirect()->route('smart-devices.index')
                ->with('error', 'An error occurred during authorization: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect the user's Amazon account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disconnect()
    {
        try {
            $platform = $this->smartHomeService->getPlatformBySlug('alexa');

            if ($platform) {
                // Update the platform to mark it as inactive and clear credentials
                $this->smartHomeService->updatePlatform($platform->id, [
                    'is_active' => false,
                    'credentials' => null,
                ]);
            }

            return redirect()->route('smart-devices.index')
                ->with('success', 'Amazon Alexa account disconnected successfully.');
        } catch (\Exception $e) {
            Log::error('Exception during Amazon disconnect: ' . $e->getMessage());
            return redirect()->route('smart-devices.index')
                ->with('error', 'An error occurred while disconnecting: ' . $e->getMessage());
        }
    }
}
