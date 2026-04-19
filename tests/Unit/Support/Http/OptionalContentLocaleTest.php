<?php

namespace Tests\Unit\Support\Http;

use App\Support\Http\OptionalContentLocale;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class OptionalContentLocaleTest extends ServiceMysqlTestCase
{
    public function test_blank_content_locale_returns_null(): void
    {
        $request = Request::create('/', 'GET', ['content_locale' => '  ']);

        $this->assertNull(OptionalContentLocale::languageIdOrNull($request));
    }

    public function test_missing_content_locale_returns_null(): void
    {
        $request = Request::create('/', 'GET');

        $this->assertNull(OptionalContentLocale::languageIdOrNull($request));
    }

    public function test_valid_code_returns_language_id(): void
    {
        $request = Request::create('/', 'GET', ['content_locale' => 'pt_BR']);

        $this->assertSame(2, OptionalContentLocale::languageIdOrNull($request));
    }

    public function test_unknown_code_throws_validation_exception(): void
    {
        $request = Request::create('/', 'GET', ['content_locale' => 'xx_YY']);

        $this->expectException(ValidationException::class);
        OptionalContentLocale::languageIdOrNull($request);
    }
}
