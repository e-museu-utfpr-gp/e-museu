<?php

namespace App\Support\Mail;

/**
 * Declarative readiness checks per transport (`config/mail.php` → `transport_required_config`).
 * When adding mailers, extend that map and cover behaviour in
 * `tests/Unit/Support/Mail/OutgoingMailIsConfiguredTest.php`.
 * Operational variables are documented in `.env.example` and README (Outgoing mail).
 */
class OutgoingMailIsConfigured
{
    /**
     * Whether the default Laravel mailer is configured enough to attempt outgoing mail.
     * This only catches obvious misconfiguration (missing API keys, host, etc.), not runtime delivery failures.
     */
    public static function forDefaultMailer(): bool
    {
        return self::forMailer((string) config('mail.default'));
    }

    public static function forMailer(string $name): bool
    {
        $mailers = config('mail.mailers', []);
        $config = $mailers[$name] ?? null;
        if (! is_array($config)) {
            return false;
        }

        $transport = (string) ($config['transport'] ?? $name);

        return match ($transport) {
            'array', 'log', 'sendmail', 'mail' => true,
            'null' => false,
            'smtp' => filled((string) ($config['host'] ?? '')),
            'failover' => self::multiMailerHasReadyChild($config),
            'roundrobin' => self::multiMailerHasReadyChild($config),
            default => self::transportMeetsDeclaredRequirements($transport),
        };
    }

    private static function transportMeetsDeclaredRequirements(string $transport): bool
    {
        $req = config("mail.transport_required_config.{$transport}");
        if ($req === null) {
            return false;
        }
        if (is_string($req)) {
            return filled((string) config($req));
        }
        if (is_array($req)) {
            foreach ($req as $path) {
                if (! is_string($path) || ! filled((string) config($path))) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Failover / roundrobin: non-production accepts any ready child (e.g. log for local/tests).
     * In production, at least one ready child must not be a log/array sink so verification is not gated on “only logs”.
     *
     * @param  array<string, mixed>  $multiMailerConfig
     */
    private static function multiMailerHasReadyChild(array $multiMailerConfig): bool
    {
        if (config('app.env') === 'production') {
            return self::anyProductionDeliverableChildReady($multiMailerConfig);
        }

        return self::anyChildMailerReady($multiMailerConfig);
    }

    /**
     * @param  array<string, mixed>  $multiMailerConfig
     */
    private static function anyChildMailerReady(array $multiMailerConfig): bool
    {
        $names = $multiMailerConfig['mailers'] ?? [];
        if (! is_array($names) || $names === []) {
            return false;
        }

        foreach ($names as $mailerName) {
            if (self::forMailer((string) $mailerName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $multiMailerConfig
     */
    private static function anyProductionDeliverableChildReady(array $multiMailerConfig): bool
    {
        $names = $multiMailerConfig['mailers'] ?? [];
        if (! is_array($names) || $names === []) {
            return false;
        }

        foreach ($names as $mailerName) {
            $name = (string) $mailerName;
            if (! self::forMailer($name)) {
                continue;
            }
            if (! self::isNonDeliveringSinkMailer($name)) {
                return true;
            }
        }

        return false;
    }

    private static function isNonDeliveringSinkMailer(string $name): bool
    {
        $mailers = config('mail.mailers', []);
        $mailerConfig = $mailers[$name] ?? null;
        if (! is_array($mailerConfig)) {
            return true;
        }
        $transport = (string) ($mailerConfig['transport'] ?? $name);

        return in_array($transport, ['log', 'array'], true);
    }
}
