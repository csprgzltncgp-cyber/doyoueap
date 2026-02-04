<?php

namespace Tests\Unit;

use App\Services\Vimeo\VimeoUploadService;
use ReflectionMethod;
use Tests\TestCase;

class VimeoUploadServiceTest extends TestCase
{
    public function test_infer_public_url_extracts_video_id_correctly(): void
    {
        $service = new VimeoUploadService;

        $method = new ReflectionMethod($service, 'inferPublicUrl');
        $method->setAccessible(true);

        // Standard video URI
        $result = $method->invoke($service, '/videos/123456789');
        $this->assertEquals('https://vimeo.com/123456789', $result);
    }

    public function test_infer_public_url_handles_video_ids_starting_with_video_letters(): void
    {
        $service = new VimeoUploadService;

        $method = new ReflectionMethod($service, 'inferPublicUrl');
        $method->setAccessible(true);

        // Video ID starting with "video" characters (edge case that was broken with ltrim)
        $result = $method->invoke($service, '/videos/video123');
        $this->assertEquals('https://vimeo.com/video123', $result);

        // Video ID starting with "s" (would have been stripped by ltrim)
        $result = $method->invoke($service, '/videos/special999');
        $this->assertEquals('https://vimeo.com/special999', $result);
    }

    public function test_infer_public_url_returns_null_for_invalid_uri(): void
    {
        $service = new VimeoUploadService;

        $method = new ReflectionMethod($service, 'inferPublicUrl');
        $method->setAccessible(true);

        $result = $method->invoke($service, '/users/123');
        $this->assertNull($result);

        $result = $method->invoke($service, 'https://vimeo.com/123');
        $this->assertNull($result);
    }
}
