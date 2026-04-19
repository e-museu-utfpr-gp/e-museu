<?php

namespace Tests\Feature\Catalog;

use PHPUnit\Framework\Attributes\Group;
use Tests\Support\AbstractMysqlRefreshDatabaseTestCase;

#[Group('mysql')]
class OptionalContentLocaleJsonTest extends AbstractMysqlRefreshDatabaseTestCase
{
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
