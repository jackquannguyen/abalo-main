<?php

namespace Tests\Feature;

use App\Models\AbUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AbaloLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_correct_password_and_creates_verified_session(): void
    {
        $user = AbUser::create([
            'ab_name' => 'seller',
            'ab_mail' => 'seller@example.test',
            'ab_password' => Hash::make('good-password'),
        ]);

        $this->post('/login', ['username' => 'seller', 'password' => 'wrong-password'])
            ->assertSessionHasErrors('username');
        $this->assertNull(session('abalo_user_id'));

        $this->post('/login', ['username' => 'seller', 'password' => 'good-password'])
            ->assertRedirect('/demo/offer');
        $this->assertSame($user->id, session('abalo_user_id'));

        $this->post('/logout')->assertRedirect('/login');
        $this->assertNull(session('abalo_user_id'));
    }
}
