<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $result = $this->authService->login($request->email, $request->password);
        } catch (AuthenticationException $e) {
            return error('Invalid credentials');
        }

        $user = $result['user'];
        $resource = new UserResource($user);

        return success('Login successful',[
            'user' => $resource,
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
        ], 200);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return ApiResponse::success(null, 'Logged out successfully');
    }

    public function user(Request $request)
    {
        $user = $this->authService->currentUser($request->user())->load(['company', 'branch']);
        return ApiResponse::success(new UserResource($user), 'Current user fetched successfully');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'sometimes|string|max:20',
            'mobile' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fillableFields = ['name', 'email', 'phone', 'mobile', 'address'];
        $data = [];

        // Capture provided fields, even if empty strings are sent
        foreach ($fillableFields as $field) {
            if ($request->exists($field)) {
                $data[$field] = $request->input($field);
            }
        }

        // Update image if provided
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // Only update if data is not empty
        if (!empty($data)) {
            $user->update($data);
        }

        $user = $user->fresh()->load(['company', 'branch']);

        return ApiResponse::success(new UserResource($user), 'Profile updated successfully');
    }
}
