<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    /**
     * แสดงรายชื่อผู้ใช้ทั้งหมด
     * GET /api/admin/users
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $searchTerm = $request->query('search');

        $query = User::where('id', '!=', auth()->id());

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('username', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $users = $query->latest()->paginate($perPage)->withQueryString();

        return response()->json($users);
    }

    /**
     * สร้างผู้ใช้ใหม่
     * POST /api/admin/users
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'status' => ['nullable', Rule::in(['active', 'deactive'])],
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'] ?? null,
            'password' => Hash::make($validatedData['password']),
            'status' => $validatedData['status'] ?? 'active',
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201);
    }

    /**
     * แสดงข้อมูลผู้ใช้คนเดียว
     * GET /api/admin/users/{user}
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * อัปเดตข้อมูลผู้ใช้
     * PUT /api/admin/users/{user}
     */
    public function update(Request $request, User $user)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'username' => ['sometimes', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone' => 'sometimes|nullable|string|max:255',
                'password' => 'sometimes|string|min:8|confirmed',
                'status' => ['sometimes', Rule::in(['active', 'deactive'])],
            ]);

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);

            return response()->json([
                'message' => 'User updated successfully.',
                'user' => $user->fresh(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ลบผู้ใช้
     * DELETE /api/admin/users/{user}
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
