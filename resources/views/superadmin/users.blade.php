<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Sistem Pengajuan Barang Dapur MBG</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-red-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">Sistem Pengajuan Barang Dapur MBG</h1>
                        <p class="text-red-200">Kelola User</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span>Selamat datang, {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-700 hover:bg-red-800 px-4 py-2 rounded-lg">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="bg-white shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex space-x-8 py-3">
                    <a href="{{ route('superadmin.dashboard') }}" class="text-gray-600 hover:text-red-600">Dashboard</a>
                    <a href="{{ route('superadmin.users') }}" class="text-red-600 font-semibold border-b-2 border-red-600 pb-2">Kelola User</a>
                    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Admin Panel</a>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="container mx-auto px-4 py-2">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="container mx-auto px-4 py-2">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Daftar User</h2>
                <button onclick="toggleForm()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    + Tambah User
                </button>
            </div>

            <!-- User List -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Semua User</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bergabung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($user->role === 'super_admin') bg-red-100 text-red-800
                                            @elseif($user->role === 'admin') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($user->is_active) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('superadmin.users.toggle-status', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="{{ $user->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Add User Form -->
            <div id="addUserForm" class="bg-white rounded-lg shadow mt-6" style="display: none;">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah User Baru</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Masukkan nama" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Masukkan email" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="staf">Staf</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Masukkan password" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Konfirmasi password" required>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="toggleForm()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                                Tambah User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg font-medium text-gray-900">Edit User</h3>
                        <form id="editUserForm" method="POST" class="mt-4 space-y-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="user_id" id="edit_user_id">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 text-left">Nama Lengkap</label>
                                <input type="text" name="name" id="edit_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 text-left">Email</label>
                                <input type="email" name="email" id="edit_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 text-left">Role</label>
                                <select name="role" id="edit_role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="staf">Staf</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 text-left">Password (biarkan kosong jika tidak ingin mengubah)</label>
                                <input type="password" name="password" id="edit_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 text-left">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="edit_password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            
                            <div class="flex justify-end space-x-4 mt-6">
                                <button type="button" onclick="closeEditModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                                    Batal
                                </button>
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleForm() {
            const form = document.getElementById('addUserForm');
            const currentDisplay = window.getComputedStyle(form).display;
            
            if (currentDisplay === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        function openEditModal(userId, name, email, role) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_password_confirmation').value = '';
            
            document.getElementById('editUserModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        // Handle form submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userId = document.getElementById('edit_user_id').value;
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.textContent = 'Menyimpan...';
            
            // Create form data manually to ensure all fields are sent
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('_method', 'PUT');
            formData.append('name', document.getElementById('edit_name').value);
            formData.append('email', document.getElementById('edit_email').value);
            formData.append('role', document.getElementById('edit_role').value);
            formData.append('password', document.getElementById('edit_password').value);
            formData.append('password_confirmation', document.getElementById('edit_password_confirmation').value);
            
            fetch(`/super-admin/users/${userId}`, {
                method: 'POST', // Use POST for FormData with _method override
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message, close modal and reload
                    alert(data.message);
                    closeEditModal();
                    location.reload();
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate user');
            })
            .finally(() => {
                // Reset button state
                submitButton.disabled = false;
                submitButton.textContent = 'Simpan Perubahan';
            });
        });

        // Auto-hide form after successful submission
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('addUserForm').style.display = 'none';
            });
        @endif

        // Ensure form is hidden on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('addUserForm').style.display = 'none';
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editUserModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
