<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;

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

            // Determine which token was provided
            $googleUser = null;

            if ($request->has('id_token') && !empty($request->id_token)) {
                // Get user info from Google using the ID token
                $googleUser = Socialite::driver('google')
                    ->stateless()
                    ->userFromToken($request->id_token);
            } elseif ($request->has('access_token') && !empty($request->access_token)) {
                // Get user info from Google using the access token
                $googleUser = Socialite::driver('google')
                    ->stateless()
                    ->userFromToken($request->access_token);
            }

            if (!$googleUser) {
                return response()->json(['error' => 'Failed to authenticate with Google'], 401);
            }

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
            \Log::error('Google authentication error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
