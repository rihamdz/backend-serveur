<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        if (!Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification not enabled.');
        }
    
        $user = User::factory()->withPersonalTeam()->unverified()->create();
    
        $verificationUrl = URL::temporarySignedRoute(
            'api.verify-email', // Update the route name
            now()->addMinutes(60),
            ['token' => $user->verification_token] // Use the token parameter
        );
        
    
        $response = $this->actingAs($user)->get($verificationUrl);
    
        $response->assertStatus(200);
    }
    
    public function test_email_can_be_verified(): void
    {
        if (! Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification not enabled.');
        }
    
        Event::fake();
    
        $user = User::factory()->unverified()->create();
    
        $verificationUrl = URL::temporarySignedRoute(
            'api.verify-email', // Update the route name
            now()->addMinutes(60),
            ['token' => $user->verification_token] // Use the token parameter
        );
        
    
        $response = $this->actingAs($user)->get($verificationUrl);
    
        Event::assertDispatched(Verified::class);
    
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }
    
    

    public function test_email_can_not_verified_with_invalid_hash(): void
    {
        if (! Features::enabled(Features::emailVerification())) {
            $this->markTestSkipped('Email verification not enabled.');
        }

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
