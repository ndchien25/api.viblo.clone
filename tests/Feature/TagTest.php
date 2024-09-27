<?php

namespace Tests\Feature;

use App\Models\Tag;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TagTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_returns_empty_array_when_no_tags_found()
    {
        // Thực hiện yêu cầu tìm kiếm thẻ không tồn tại
        $response = $this->actingAs($this->user)->getJson('/api/v1/tags/search?search=nonexistenttag');

        // Xác minh phản hồi
        $response->assertStatus(200)
            ->assertJson([]);
    }

    #[Test]
    public function it_can_search_tags()
    {
        $searchQuery = 'sus';
        $response = $this->actingAs($this->user)->getJson('/api/v1/tags/search?search='.$searchQuery);

        // Xác minh phản hồi
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                ]
            ]);
    }
}
