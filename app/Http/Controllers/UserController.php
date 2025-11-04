<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\User\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
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
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roleOptions = ['customer' => 'Customer', 'stylist' => 'Stylist', 'admin' => 'Admin'];
        return view('users.create', compact('roleOptions'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \App\Http\Requests\User\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Hash password
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('users.index')
            ->with('status', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // Prevent editing own account from user management
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Gunakan profile settings untuk mengubah akun Anda sendiri.');
        }

        $roleOptions = ['customer' => 'Customer', 'stylist' => 'Stylist', 'admin' => 'Admin'];
        return view('users.edit', compact('user', 'roleOptions'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \App\Http\Requests\User\StoreUserRequest  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, User $user)
    {
        // Prevent editing own account from user management
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Gunakan profile settings untuk mengubah akun Anda sendiri.');
        }

        $data = $request->validated();

        // Only hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('status', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Prevent deleting if user is associated with stylist profile
        if ($user->isStylist() && $user->stylist) {
            return redirect()->back()
                ->with('error', 'Hapus stylist profile terlebih dahulu sebelum menghapus user.');
        }

        // Prevent deleting if user has bookings as customer
        if ($user->isCustomer() && $user->bookings()->exists()) {
            return redirect()->back()
                ->with('error', 'User ini memiliki riwayat booking. Tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('status', 'User berhasil dihapus.');
    }
}
