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
        $perPage = $request->query('per_page', 5);

        $searchTerm = $request->query('search');

        $query = User::where('id', '!=', auth()->id());

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('username', 'like', "%{$searchTerm}%")
                    ->orWhere('role', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%");
            });
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
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'user'])], // จำกัด role ที่สร้างได้
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user
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
                'status' => ['sometimes', Rule::in(['active', 'deactive'])],
            ]);

            if (isset($validatedData['status'])) {
                $user->status = $validatedData['status'];
            }

            $user->save();

            return response()->json([
                'message' => 'User updated successfully.',
                'user' => $user
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ลบผู้ใช้
     * DELETE /api/admin/users/{user}
     */
    public function destroy(User $user)
    {
        // ป้องกันการลบ superadmin คนอื่น (ถ้ามี) หรือลบตัวเอง
        if ($user->role === 'superadmin' || $user->id === auth()->id()) {
            return response()->json(['message' => 'Action not allowed.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
