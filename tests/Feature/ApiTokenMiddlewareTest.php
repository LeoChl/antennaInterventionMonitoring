<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ApiTokenMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.token' => 'secret-token']);

        Route::middleware('api.token')->post('/api/_protected', fn () => response()->json(['ok' => true]));
    }

    public function testRejectsRequestWithoutToken(): void
    {
        $this->postJson('/api/_protected')
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate', 'Bearer');
    }

    public function testRejectsRequestWithInvalidToken(): void
    {
        $this->postJson('/api/_protected', [], ['Authorization' => 'Bearer nope'])
            ->assertUnauthorized();
    }

    public function testAcceptsRequestWithValidToken(): void
    {
        $this->postJson('/api/_protected', [], ['Authorization' => 'Bearer secret-token'])
            ->assertOk();
    }

    public function testRejectsEverythingWhenNoTokenIsConfigured(): void
    {
        config(['services.api.token' => null]);

        $this->postJson('/api/_protected', [], ['Authorization' => 'Bearer '])
            ->assertUnauthorized();
    }
}
