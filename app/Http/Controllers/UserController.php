<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('superadmin.users', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:staf,admin,super_admin',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            return redirect()->route('superadmin.users')->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'nullable|in:staf,admin,super_admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $updateData = [];
            $hasChanges = false;

            // Debug: Log the request data
            \Log::info('Update User Request:', [
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'filled_name' => $request->filled('name'),
                'filled_email' => $request->filled('email'),
                'filled_role' => $request->filled('role'),
                'filled_password' => $request->filled('password'),
            ]);

            // Always update fields that are provided, even if same value
            if ($request->filled('name')) {
                $updateData['name'] = $request->name;
                $hasChanges = true;
            }

            if ($request->filled('email')) {
                $updateData['email'] = $request->email;
                $hasChanges = true;
            }

            if ($request->filled('role')) {
                $updateData['role'] = $request->role;
                $hasChanges = true;
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                $hasChanges = true;
            }

            // Check if there's any data to update
            if (!$hasChanges) {
                \Log::info('No changes detected for user update', [
                    'user_id' => $user->id,
                    'update_data' => $updateData
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang diubah'
                ], 400);
            }

            $user->update($updateData);

            \Log::info('User updated successfully', [
                'user_id' => $user->id,
                'update_data' => $updateData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri!');
            }

            $user->delete();

            return redirect()->route('superadmin.users')->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        try {
            // Prevent self-deactivation
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Tidak dapat menonaktifkan akun sendiri!');
            }

            $user->update([
                'is_active' => !$user->is_active
            ]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "User berhasil {$status}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
