<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Services\ProvisionTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    private const STATUS_VALID = ['aktif', 'suspended'];

    /**
     * Daftar akun (direktori). Mendukung pencarian & pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($search !== '', fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('id')
            ->paginate($request->integer('per_page', 20));

        return UserResource::collection($users);
    }

    /**
     * Buat akun baru, verifikasi langsung, dan siapkan tenant.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateUser($request, create: true);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'aktif',
            'plan' => 'pro',
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        ProvisionTenant::provision($user);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Detail akun.
     */
    public function show(Request $request, User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Perbarui identitas akun. Password opsional.
     */
    public function update(Request $request, User $user): UserResource
    {
        $data = $this->validateUser($request, create: false, user: $user);

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return new UserResource($user);
    }

    /**
     * Aktifkan / bekukan akun.
     */
    public function status(Request $request, User $user): UserResource
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUS_VALID)],
        ]);

        $user->status = $validated['status'];
        $user->save();

        return new UserResource($user);
    }

    /**
     * Hapus akun. Akun terakhir tidak boleh dihapus.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if (User::count() <= 1) {
            throw ValidationException::withMessages([
                'user' => 'Akun terakhir tidak dapat dihapus.',
            ]);
        }

        $user->delete();

        return response()->json(['message' => 'Akun dihapus.'], 200);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateUser(Request $request, bool $create, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => array_merge(
                $create ? ['required'] : ['sometimes', 'nullable'],
                ['string', 'confirmed', Password::defaults()],
            ),
        ]);

        if ($create) {
            $data['password'] = (string) $data['password'];
        } else {
            $data['password'] = (string) ($data['password'] ?? '');
        }

        return $data;
    }
}
