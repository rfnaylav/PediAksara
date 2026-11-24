<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->whereIn('role', ['anggota', 'pustakawan'])->orderBy('name')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['account' => new User()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['member_code'] = $data['role'] === 'anggota' ? $this->newMemberCode() : null;
        User::create($data);

        return redirect()->route('users.index')->with('status', 'Akun berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        abort_if($user->hasRole('admin'), 404);

        return view('admin.users.form', ['account' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->hasRole('admin'), 404);
        $data = $this->validated($request, $user);
        $data['member_code'] = $data['role'] === 'anggota'
            ? ($user->member_code ?? $this->newMemberCode())
            : null;
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('users.index')->with('status', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->hasRole('admin'), 404);
        if ($user->loans()->exists() || $user->loansAsLibrarian()->exists()) {
            return back()->withErrors(['account' => 'Akun memiliki riwayat transaksi dan tidak dapat dihapus.']);
        }
        $user->delete();

        return redirect()->route('users.index')->with('status', 'Akun berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(['anggota', 'pustakawan'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:2000'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ]);
    }

    private function newMemberCode(): string
    {
        do {
            $code = 'AGT'.Str::upper(Str::random(8));
        } while (User::query()->where('member_code', $code)->exists());

        return $code;
    }
}
