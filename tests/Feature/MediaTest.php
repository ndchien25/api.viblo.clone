<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MediaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected static function mockPresignedUrl()
    {
        $storage = Mockery::mock('League\Flysystem\Adapter\Local');

        $storage->shouldReceive('getAdapter')->andReturn($storage);
        $storage->shouldReceive('getClient')->andReturn($storage);
        $storage->shouldReceive('getBucket')->andReturn(env('AWS_BUCKET'));
        $storage->shouldReceive('getCommand')->andReturn($storage);
        $storage->shouldReceive('createPresignedRequest')->andReturn($storage);
        $storage->shouldReceive('getUri')->andReturn(env('AWS_ENDPOINT'));
        $storage->shouldReceive('put')->andReturn(true); // Simulate file upload
        $storage->shouldReceive('url')->andReturn(env('AWS_ENDPOINT'));
        return $storage;
    }

    #[Test]
    public function uploadMediaWithoutAuth()
    {
        $data = [
            'file_name' => 'jpg',
        ];

        $response = $this->postJson('/api/v1/upload', $data);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function uploadMediaWithAuth()
    {
        $this->actingAs($this->user);
        $storage = $this->mockPresignedUrl();
        Storage::set('s3', $storage);
        $data = ['file_name' => 'jpg'];

        $response = $this->postJson('/api/v1/upload', $data);

        $response->assertStatus(200)->assertJsonStructure([
            'file_path',
            'pre_signed',
        ]);
    }

    public function itCanGetObject()
    {
        $filePath = '1/gallery/example.jpg';
        Storage::disk('s3')->put($filePath, 'content');

        $response = $this->getJson('/api/v1/get-object?file_path=' . $filePath);

        $response->assertStatus(200)->assertJsonStructure(['url'])->assertJson(['url' => Storage::disk('s3')->url($filePath)]);
    }

    public function getObjectFailsWithoutFilePath()
    {
        $response = $this->getJson('/api/v1/get-object');

        $response->assertStatus(422)->assertJsonValidationErrors(['file_path']);
    }
}
