<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\User\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $roleFilter = $request->get('role');

        $query = User::orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . $search . '%');
        }

        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->paginate(15)->appends($request->only('search', 'role'));

        $roleOptions = ['customer' => 'Customer', 'stylist' => 'Stylist', 'admin' => 'Admin'];

        return view('users.index', compact('users', 'search', 'roleFilter', 'roleOptions'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roleOptions = ['customer' => 'Customer', 'stylist' => 'Stylist', 'admin' => 'Admin'];
        return view('users.create', compact('roleOptions'));
    }

    /**
     * Menyimpan pengguna baru ke dalam penyimpanan.
     *
     * @param  \App\Http\Requests\User\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Handle unggahan gambar
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $data['image'] = $imagePath;
        }

        User::create($data);

        return redirect()->route('users.index')
            ->with('status', 'User berhasil ditambahkan.');
    }

    /**
     * Show form untuk mengedit pengguna yang ditentukan.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // Mencegah pengeditan akun sendiri dari manajemen pengguna
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Gunakan profile settings untuk mengubah akun Anda sendiri.');
        }

        $roleOptions = ['customer' => 'Customer', 'stylist' => 'Stylist', 'admin' => 'Admin'];
        return view('users.edit', compact('user', 'roleOptions'));
    }

    /**
     * update spesifik user di storage.
     *
     * @param  \App\Http\Requests\User\StoreUserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Gunakan profile settings untuk mengubah akun Anda sendiri.');
        }

        $data = $request->validated();

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('image')) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('images', 'public');
            $data['image'] = $imagePath;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('status', 'User berhasil diperbarui.');
    }

    /**
     * Remove user yang ditentukan dari storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->isStylist() && $user->stylist) {
            return redirect()->back()
                ->with('error', 'Hapus stylist profile terlebih dahulu sebelum menghapus user.');
        }

        if ($user->isCustomer() && $user->bookings()->exists()) {
            return redirect()->back()
                ->with('error', 'User ini memiliki riwayat booking. Tidak dapat dihapus.');
        }

        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('status', 'User berhasil dihapus.');
    }
}
