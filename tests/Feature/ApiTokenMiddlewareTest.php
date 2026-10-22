<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

final class ApiTokenMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.token' => 'secret-token']);

        Route::middleware('api.token')->post('/api/_protected', fn () => response()->json(['ok' => true]));
    }

    #[TestDox('Given no token is provided, when an endpoint is called, then the request is rejected')]
    public function test_rejects_request_without_token(): void
    {
        $this->postJson('/api/_protected')
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate', 'Bearer');
    }

    #[TestDox('Given an invalid token is provided, when an endpoint is called, then the request is rejected')]
    public function test_rejects_request_with_invalid_token(): void
    {
        $this->postJson('/api/_protected', [], ['Authorization' => 'Bearer nope'])
            ->assertUnauthorized();
    }

    #[TestDox('Given a valid token is provided, when protected endpoint is called, then the request is accepted')]
    public function test_accepts_request_with_valid_token(): void
    {
        $this->postJson('/api/_protected', [], ['Authorization' => 'Bearer secret-token'])
            ->assertOk();
    }

    #[TestDox('Given no token configured, when a protected endpoint is called, then the request is rejected')]
    public function test_rejects_everything_when_no_token_is_configured(): void
    {
        config(['services.api.token' => null]);

        $this->postJson('/api/_protected', [], ['Authorization' => 'Bearer '])
            ->assertUnauthorized();
    }
}
