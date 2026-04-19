<?php

namespace Tests\Unit\Support\Catalog;

use App\Support\Catalog\PublicCatalogContributionOutcome;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PublicCatalogContributionOutcomeTest extends TestCase
{
    public function test_ok_does_not_throw(): void
    {
        $this->expectNotToPerformAssertions();

        PublicCatalogContributionOutcome::throwUnlessOk(['status' => 'ok']);
    }

    public function test_internal_blocked_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);

        PublicCatalogContributionOutcome::throwUnlessOk(['status' => 'internal_blocked']);
    }

    public function test_collaborator_invalid_throws_validation_exception(): void
    {
        $this->expectException(ValidationException::class);

        PublicCatalogContributionOutcome::throwUnlessOk(['status' => 'collaborator_invalid']);
    }

    public function test_email_unverified_throws_validation_exception_on_email_field(): void
    {
        try {
            PublicCatalogContributionOutcome::throwUnlessOk(['status' => 'email_unverified']);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors());
        }
    }

    public function test_collaborator_blocked_throws_validation_exception_on_email_field(): void
    {
        try {
            PublicCatalogContributionOutcome::throwUnlessOk(['status' => 'collaborator_blocked']);
            $this->fail('Expected ValidationException');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors());
        }
    }

    public function test_unknown_status_throws_invalid_argument(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        PublicCatalogContributionOutcome::throwUnlessOk(['status' => 'weird']);
    }
}
