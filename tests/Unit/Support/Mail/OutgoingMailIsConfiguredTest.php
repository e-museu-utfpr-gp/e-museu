<?php

namespace Tests\Unit\Support\Mail;

use App\Support\Mail\OutgoingMailIsConfigured;
use Tests\TestCase;

class OutgoingMailIsConfiguredTest extends TestCase
{
    public function test_resend_without_key_is_not_configured(): void
    {
        config([
            'mail.default' => 'resend',
            'services.resend.key' => '',
        ]);

        $this->assertFalse(OutgoingMailIsConfigured::forDefaultMailer());
    }

    public function test_resend_with_key_is_configured(): void
    {
        config([
            'mail.default' => 'resend',
            'services.resend.key' => 're_test_key',
        ]);

        $this->assertTrue(OutgoingMailIsConfigured::forDefaultMailer());
    }

    public function test_array_mailer_is_ready(): void
    {
        config(['mail.default' => 'array']);

        $this->assertTrue(OutgoingMailIsConfigured::forDefaultMailer());
    }

    public function test_failover_is_ready_when_fallback_mailer_is_ready(): void
    {
        config([
            'mail.default' => 'failover',
            'services.resend.key' => '',
        ]);

        $this->assertTrue(OutgoingMailIsConfigured::forDefaultMailer());
    }

    public function test_failover_is_not_ready_in_production_when_only_sink_mailers_are_ready(): void
    {
        config([
            'app.env' => 'production',
            'mail.default' => 'failover',
            'services.resend.key' => '',
        ]);

        $this->assertFalse(OutgoingMailIsConfigured::forDefaultMailer());
    }

    public function test_failover_is_ready_in_production_when_primary_mailer_is_configured(): void
    {
        config([
            'app.env' => 'production',
            'mail.default' => 'failover',
            'services.resend.key' => 're_live_key',
        ]);

        $this->assertTrue(OutgoingMailIsConfigured::forDefaultMailer());
    }

    public function test_unknown_transport_without_transport_required_config_entry_is_not_configured(): void
    {
        config([
            'mail.default' => 'custom',
            'mail.mailers.custom' => [
                'transport' => 'ses',
            ],
        ]);

        $this->assertFalse(OutgoingMailIsConfigured::forDefaultMailer());
    }
}
