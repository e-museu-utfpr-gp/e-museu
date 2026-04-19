<?php

namespace Tests\Unit\Support\Catalog;

use App\Support\Catalog\CatalogVerifyMailError;
use Tests\TestCase;

/**
 * Contract: {@see CatalogVerifyMailError::json} message masking rules (no HTTP stack).
 */
final class CatalogVerifyMailErrorJsonTest extends TestCase
{
    private string $savedAppEnv = '';

    private bool $savedAppDebug = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->savedAppEnv = (string) config('app.env');
        $this->savedAppDebug = (bool) config('app.debug');
    }

    protected function tearDown(): void
    {
        config(['app.env' => $this->savedAppEnv, 'app.debug' => $this->savedAppDebug]);
        parent::tearDown();
    }

    public function test_json_masks_message_in_production_without_debug(): void
    {
        config(['app.env' => 'production', 'app.debug' => false]);

        $response = CatalogVerifyMailError::json('mail_not_configured');

        $this->assertSame(422, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertSame(__('app.collaborator.verify_service_unavailable'), $data['message']);
    }

    public function test_json_exposes_detail_when_not_production_masked(): void
    {
        config(['app.env' => 'local', 'app.debug' => false]);

        $response = CatalogVerifyMailError::json('mail_not_configured');
        $data = $response->getData(true);

        $this->assertSame(__('app.collaborator.verify_mail_not_configured'), $data['message']);
    }

    public function test_json_send_failed_reason_uses_send_failed_message_when_visible(): void
    {
        config(['app.env' => 'local', 'app.debug' => true]);

        $response = CatalogVerifyMailError::json('send_failed');
        $data = $response->getData(true);

        $this->assertSame(__('app.collaborator.verify_mail_send_failed'), $data['message']);
    }
}
