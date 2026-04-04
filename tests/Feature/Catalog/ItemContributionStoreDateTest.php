<?php

namespace Tests\Feature\Catalog;

use App\Models\Catalog\Item;
use App\Models\Catalog\ItemCategory;
use Database\Factories\Catalog\ItemCategoryFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

        // CollaboratorRequest uses email:rfc,dns — reserved domains like example.com often fail DNS (no MX).
        $response = $this->post(route('catalog.items.store'), [
            'full_name' => 'Contribuinte Teste',
            'contact' => 'emuseu.contrib.' . uniqid('', false) . '@google.com',
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
    }
}
