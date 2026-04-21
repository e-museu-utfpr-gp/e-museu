<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Identity\Admin;
use Illuminate\Testing\PendingCommand;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
final class CreateAdminCommandTest extends AbstractMysqlRefreshDatabaseTestCase
{
    public function test_create_admin_command_persists_hashed_password(): void
    {
        $username = 'cli_admin_' . uniqid('', false);
        $plain = 'Str0ng!Cli-Secret';

        $pending = $this->artisan('create:admin');
        $this->assertInstanceOf(PendingCommand::class, $pending);
        $pending
            ->expectsQuestion('Administrator username:', $username)
            ->expectsQuestion('Administrator password:', $plain)
            ->assertExitCode(0)
            // PendingCommand runs on __destruct unless executed; DB assertions must come after run().
            ->run();

        $row = Admin::query()->where('username', $username)->first();
        $this->assertNotNull($row);
        $this->assertTrue(Hash::check($plain, (string) $row->password));
    }
}
