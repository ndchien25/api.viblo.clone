<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TagTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function emptyArrayWhenNoTagsFound()
    {
        $response = $this->actingAs($this->user)->getJson('/api/v1/tags/search?search=nonexistenttag');

        $response->assertStatus(200)->assertJson([]);
    }

    #[Test]
    public function searchTags()
    {
        $searchQuery = 'sus';
        $response = $this->actingAs($this->user)->getJson('/api/v1/tags/search?search=' . $searchQuery);

        // Xác minh phản hồi
        $response->assertStatus(200)->assertJsonStructure([
            '*' => [
                'id',
                'name',
            ]
        ]);
    }
}
