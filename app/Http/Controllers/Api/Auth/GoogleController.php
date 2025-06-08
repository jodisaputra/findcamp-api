<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToProvider()
    {
        return response()->json([
            'url' => Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl(),
        ]);
    }

    /**
     * Handle Google callback for web
     */
    public function handleProviderCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // First check if the user already exists by email
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // User doesn't exist, create a new one
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'password' => bcrypt(rand(1, 10000)), // Random password
                ]);
            } else {
                // User exists, update the Google ID and avatar if needed
                $user->google_id = $googleUser->id;
                $user->avatar = $googleUser->avatar;
                $user->save();
            }

            // Create Passport token
            $tokenResult = $user->createToken('GoogleToken');
            $accessToken = $tokenResult->accessToken;

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->token->expires_at,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Detailed error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle direct token exchange from Flutter app
     */
    public function handleToken(Request $request)
    {
        try {
            // Validate request - accept either ID token or access token
            $validator = Validator::make($request->all(), [
                'id_token' => 'required_without:access_token',
                'access_token' => 'required_without:id_token',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 400);
            }

            $googleUserData = null;

            if ($request->has('id_token') && !empty($request->id_token)) {
                // Verifikasi id_token manual ke Google
                $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $request->id_token
                ]);
                if ($response->failed()) {
                    return response()->json(['error' => 'Invalid ID token'], 401);
                }
                $googleUserData = $response->json();
            } elseif ($request->has('access_token') && !empty($request->access_token)) {
                // Verifikasi access_token manual ke Google
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $request->access_token
                ])->get('https://www.googleapis.com/oauth2/v3/userinfo');
                if ($response->failed()) {
                    return response()->json(['error' => 'Invalid access token'], 401);
                }
                $googleUserData = $response->json();
            }

            if (!$googleUserData || empty($googleUserData['email'])) {
                return response()->json(['error' => 'Failed to authenticate with Google'], 401);
            }

            // First check if the user already exists by email
            $user = User::where('email', $googleUserData['email'])->first();

            if (!$user) {
                // User doesn't exist, create a new one
                $user = User::create([
                    'name' => $googleUserData['name'] ?? ($googleUserData['given_name'] ?? 'Google User'),
                    'email' => $googleUserData['email'],
                    'google_id' => $googleUserData['sub'] ?? ($googleUserData['id'] ?? null),
                    'avatar' => $googleUserData['picture'] ?? null,
                    'password' => bcrypt(rand(1, 10000)), // Random password
                ]);
            } else {
                // User exists, update the Google ID and avatar if needed
                $user->google_id = $googleUserData['sub'] ?? ($googleUserData['id'] ?? $user->google_id);
                $user->avatar = $googleUserData['picture'] ?? $user->avatar;
                $user->save();
            }

            // Create Passport/Sanctum token
            $tokenResult = $user->createToken('GoogleToken');
            $accessToken = $tokenResult->accessToken;

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(15)->toDateTimeString(),
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Detailed error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
