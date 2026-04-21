<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Catalog;

use App\Actions\Catalog\StoreItemContribution\Concerns\StoreItemContributionRequestContext;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Services\ServiceMysqlTestCase;

#[Group('mysql')]
final class StoreItemContributionRequestContextTest extends ServiceMysqlTestCase
{
    public function test_language_id_for_validated_locale_code_matches_language_table(): void
    {
        $ctx = app(StoreItemContributionRequestContext::class);

        $this->assertSame(
            Language::idForCode('pt_BR'),
            $ctx->languageIdForValidatedLocaleCode('pt_BR')
        );
    }

    public function test_validate_store_rejects_empty_body(): void
    {
        $ctx = app(StoreItemContributionRequestContext::class);
        $request = Request::create('/catalog/items', 'POST', []);

        $this->expectException(ValidationException::class);

        $ctx->validateStore($request);
    }
}
