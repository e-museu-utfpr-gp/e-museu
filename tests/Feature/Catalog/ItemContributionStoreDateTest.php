<?php

namespace Tests\Feature\Catalog;

use App\Enums\Catalog\ItemImageType;
use App\Enums\Collaborator\CollaboratorRole;
use App\Mail\ItemContributionReceivedMail;
use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use App\Models\Collaborator\Collaborator;
use App\Services\Collaborator\CollaboratorService;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Mail, Storage};
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('mysql')]
class ItemContributionStoreDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_contribution_store_persists_release_date(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql required');
        }

        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        // Avoid UploadedFile::fake()->image(): it requires GD (imagejpeg). Host/CI PHP may omit ext-gd.
        $coverJpegB64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRof'
            . 'Hh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwh'
            . 'MjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAAR'
            . 'CAABAAEDAREAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAA'
            . 'AgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkK'
            . 'FhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWG'
            . 'h4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl'
            . '5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREA'
            . 'AgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYk'
            . 'NOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOE'
            . 'hYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk'
            . '5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD//2Q==';
        $cover = UploadedFile::fake()->createWithContent(
            'cover.jpg',
            (string) base64_decode($coverJpegB64, true)
        );

        // Public collaborator rules use email:rfc (no live DNS check).
        Mail::fake();
        Storage::fake('public');

        $email = 'emuseu.contrib.' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Contribuinte Teste',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $response = $this->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ])->post(route('catalog.items.store'), [
            'full_name' => 'Contribuinte Teste',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'Peça com data',
            'date' => '2015-06-20',
            'description' => 'Descrição mínima para validar.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertRedirect(route('catalog.items.create'));
        $response->assertSessionHas('success');

        $item = Item::query()->where('category_id', $category->id)->latest('id')->first();
        $this->assertNotNull($item);
        $this->assertSame('2015-06-20', $item->date?->format('Y-m-d'));

        $coverRow = $item->images()->where('type', ItemImageType::COVER)->first();
        $this->assertNotNull($coverRow);
        $storedPath = $coverRow->getRawOriginal('path');
        $this->assertStringStartsWith('items/' . $item->id . '/', (string) $storedPath);
        Storage::disk('public')->assertExists((string) $storedPath);

        Mail::assertSent(ItemContributionReceivedMail::class);
    }

    public function test_contribution_updates_collaborator_full_name_when_submitted_name_differs_from_record(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql required');
        }

        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        $coverJpegB64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRof'
            . 'Hh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwh'
            . 'MjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAAR'
            . 'CAABAAEDAREAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAA'
            . 'AgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkK'
            . 'FhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWG'
            . 'h4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl'
            . '5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREA'
            . 'AgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYk'
            . 'NOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOE'
            . 'hYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk'
            . '5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD//2Q==';
        $cover = UploadedFile::fake()->createWithContent(
            'cover.jpg',
            (string) base64_decode($coverJpegB64, true)
        );

        $email = 'emuseu.name-mismatch.' . uniqid('', false) . '@google.com';
        $collaborator = Collaborator::create([
            'full_name' => 'Nome Cadastrado',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        Mail::fake();

        $response = $this->withSession([
            CollaboratorService::PUBLIC_CONTRIBUTION_AUTH_SESSION_KEY => [
                'email' => $email,
                'expires_at' => now()->addMinutes(20)->getTimestamp(),
            ],
        ])->from(route('catalog.items.create'))->post(route('catalog.items.store'), [
            'full_name' => 'Outra Pessoa',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'Item teste',
            'date' => '',
            'description' => 'Descrição mínima.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertRedirect(route('catalog.items.create'));
        $response->assertSessionHas('success');

        $collaborator->refresh();
        $this->assertSame('Outra Pessoa', $collaborator->full_name);

        $this->assertSame(1, Item::query()->where('category_id', $category->id)->count());
        Mail::assertSent(ItemContributionReceivedMail::class);
    }

    public function test_contribution_rejected_when_db_verified_but_no_session_auth(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql required');
        }

        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        $coverJpegB64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRof'
            . 'Hh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwh'
            . 'MjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAAR'
            . 'CAABAAEDAREAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAA'
            . 'AgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkK'
            . 'FhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWG'
            . 'h4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl'
            . '5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREA'
            . 'AgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYk'
            . 'NOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOE'
            . 'hYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk'
            . '5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD//2Q==';
        $cover = UploadedFile::fake()->createWithContent(
            'cover.jpg',
            (string) base64_decode($coverJpegB64, true)
        );

        $email = 'emuseu.no-session.' . uniqid('', false) . '@google.com';
        Collaborator::create([
            'full_name' => 'Contribuinte Teste',
            'email' => $email,
            'role' => CollaboratorRole::EXTERNAL,
            'blocked' => false,
            'last_email_verification_at' => now(),
        ]);

        $response = $this->post(route('catalog.items.store'), [
            'full_name' => 'Contribuinte Teste',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'Sem sessão',
            'date' => '',
            'description' => 'Descrição mínima.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertSame(0, Item::query()->where('category_id', $category->id)->count());
    }

    public function test_contribution_without_verification_does_not_create_collaborator_for_new_email(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql required');
        }

        /** @var ItemCategory $category */
        $category = ItemCategoryFactory::new()->create();

        $coverJpegB64 = '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRof'
            . 'Hh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwh'
            . 'MjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAAR'
            . 'CAABAAEDAREAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAA'
            . 'AgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkK'
            . 'FhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWG'
            . 'h4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl'
            . '5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREA'
            . 'AgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYk'
            . 'NOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOE'
            . 'hYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk'
            . '5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD//2Q==';
        $cover = UploadedFile::fake()->createWithContent(
            'cover.jpg',
            (string) base64_decode($coverJpegB64, true)
        );

        Mail::fake();

        $email = 'emuseu.no-orphan.' . uniqid('', false) . '@google.com';
        $this->assertNull(Collaborator::query()->where('email', $email)->first());

        $response = $this->post(route('catalog.items.store'), [
            'full_name' => 'Novo Contribuinte',
            'email' => $email,
            'content_locale' => 'pt_BR',
            'name' => 'Peça sem verificação',
            'date' => '',
            'description' => 'Descrição mínima.',
            'detail' => '',
            'history' => '',
            'category_id' => (string) $category->id,
            'tags' => [],
            'extras' => [],
            'components' => [],
            'cover_image' => $cover,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertNull(Collaborator::query()->where('email', $email)->first());
        $this->assertSame(0, Item::query()->where('category_id', $category->id)->count());
    }
}
