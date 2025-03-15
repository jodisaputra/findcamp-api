<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'date_of_birth' => 'sometimes|nullable|date',
            'country' => 'sometimes|nullable|string|max:100',
            'profile_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get authenticated user
        $user = User::findOrFail(auth()->id());

        // Update user data
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            // Reset email verification if email changed
            $user->email_verified_at = null;
            // Here you might want to send a new verification email
        }
        
        if ($request->has('date_of_birth')) {
            $user->date_of_birth = $request->date_of_birth;
        }
        
        if ($request->has('country')) {
            $user->country = $request->country;
        }
        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->avatar && !str_contains($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new image
            $path = $request->file('profile_image')->store('avatars', 'public');
            $user->avatar = $path;
            $user->profile_image_path = Storage::url($path);
        }

        // Save user
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
}
