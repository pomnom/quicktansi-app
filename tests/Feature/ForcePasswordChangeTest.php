<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    private array $createdUserIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->createdUserIds)) {
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    private function makeUser(bool $mustChangePassword): User
    {
        $user = User::create([
            'nip' => 'TEST-' . uniqid(),
            'name' => 'Test User',
            'email' => 'test-' . uniqid() . '@example.com',
            'instansi' => 'Instansi Uji Coba',
            'is_superadmin' => false,
            'password' => Hash::make('secret'),
            'must_change_password' => $mustChangePassword,
            'email_verified_at' => now(),
        ]);

        $this->createdUserIds[] = $user->id;

        return $user;
    }

    public function test_user_with_default_password_is_redirected_to_profile(): void
    {
        $user = $this->makeUser(true);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('profile.show'));
    }

    public function test_user_with_default_password_can_still_reach_profile_page(): void
    {
        $user = $this->makeUser(true);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    public function test_user_without_flag_is_not_redirected(): void
    {
        $user = $this->makeUser(false);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
    }

    public function test_changing_password_clears_the_flag_and_stops_redirect(): void
    {
        $user = $this->makeUser(true);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'secret',
            'password' => 'newSecurePassword123',
            'password_confirmation' => 'newSecurePassword123',
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertFalse($user->fresh()->must_change_password);

        $response2 = $this->actingAs($user->fresh())->get('/');
        $response2->assertOk();
    }
}
