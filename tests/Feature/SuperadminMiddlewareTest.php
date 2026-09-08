<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperadminMiddlewareTest extends TestCase
{
    private array $createdUserIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->createdUserIds)) {
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    private function makeUser(bool $isSuperadmin): User
    {
        $user = User::create([
            'nip' => 'TEST-' . uniqid(),
            'name' => 'Test User',
            'email' => 'test-' . uniqid() . '@example.com',
            'instansi' => 'Instansi Uji Coba',
            'is_superadmin' => $isSuperadmin,
            'password' => Hash::make('secret'),
            'email_verified_at' => now(),
        ]);

        $this->createdUserIds[] = $user->id;

        return $user;
    }

    /**
     * @dataProvider superadminOnlyRoutes
     */
    public function test_superadmin_can_access_superadmin_only_route(string $uri): void
    {
        $super = $this->makeUser(true);

        $response = $this->actingAs($super)->get($uri);

        $response->assertOk();
    }

    /**
     * @dataProvider superadminOnlyRoutes
     */
    public function test_non_superadmin_is_forbidden_from_superadmin_only_route(string $uri): void
    {
        $regular = $this->makeUser(false);

        $response = $this->actingAs($regular)->get($uri);

        $response->assertForbidden();
    }

    public static function superadminOnlyRoutes(): array
    {
        return [
            'user management' => ['/user'],
            'instansi management' => ['/instansi'],
            'master rekening' => ['/master-rekening'],
        ];
    }
}
