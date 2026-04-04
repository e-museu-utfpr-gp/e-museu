<?php

namespace Tests\Feature\Catalog;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionalContentLocaleJsonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            $this->markTestSkipped(
                'Migrations require pdo_mysql (install the extension or run tests in the app Docker container).'
            );
        }

        parent::setUp();
    }

    public function test_items_by_category_returns_422_for_unknown_content_locale(): void
    {
        $this->getJson(route('catalog.items.byCategory', [
            'item_category' => '1',
            'content_locale' => '__invalid_locale__',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['content_locale']);
    }

    public function test_tags_autocomplete_returns_422_for_unknown_content_locale(): void
    {
        $this->getJson(route('catalog.tags.autocomplete', [
            'query' => 'x',
            'category' => '1',
            'content_locale' => '__invalid_locale__',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['content_locale']);
    }
}
