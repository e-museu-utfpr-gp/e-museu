<?php

namespace Tests\Feature\Middleware;

/**
 * Covers {@see \App\Http\Middleware\SetLocaleFromSession}.
 */
class SetLocaleFromSessionMiddlewareTest extends MysqlMiddlewareTestCase
{
    public function test_valid_session_locale_overrides_app_locale(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $this->withSession(['locale' => 'en'])
            ->get(route('home'))
            ->assertOk();

        $this->assertSame('en', app()->getLocale());
    }

    public function test_session_locale_without_ui_pack_is_ignored(): void
    {
        config(['app.locale' => 'pt_BR', 'app.fallback_locale' => 'en']);
        app()->setLocale('pt_BR');

        $this->withSession(['locale' => 'universal'])
            ->get(route('home'))
            ->assertOk();

        $this->assertSame('pt_BR', app()->getLocale());
    }
}
