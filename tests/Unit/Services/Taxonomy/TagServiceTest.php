<?php

namespace Tests\Unit\Services\Taxonomy;

use App\Services\Taxonomy\TagService;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('services')]
class TagServiceTest extends TestCase
{
    public function test_get_by_category_validated_only_returns_empty_meta_for_blank_category(): void
    {
        $svc = app(TagService::class);
        $result = $svc->getByCategoryValidatedOnly('');

        $this->assertTrue($result['tags']->isEmpty());
        $this->assertSame(0, $result['total']);
        $this->assertFalse($result['truncated']);
    }

    public function test_json_payload_for_public_category_select_maps_empty_category(): void
    {
        $svc = app(TagService::class);
        $payload = $svc->jsonPayloadForPublicCategorySelect('');

        $this->assertSame([], $payload['data']);
        $this->assertSame(0, $payload['meta']['total']);
    }
}
