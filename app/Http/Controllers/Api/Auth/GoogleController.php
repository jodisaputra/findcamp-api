<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
            // Validate request
            $request->validate([
                'id_token' => 'required',
            ]);

            // Get user info from Google using the ID token
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->id_token);

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

            // For Sanctum, create a personal access token and get plainTextToken directly
            $tokenResult = $user->createToken('GoogleToken');

            // With Sanctum, the plainTextToken is already available
            $accessToken = $tokenResult->accessToken;

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(15)->toDateTimeString(),
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
