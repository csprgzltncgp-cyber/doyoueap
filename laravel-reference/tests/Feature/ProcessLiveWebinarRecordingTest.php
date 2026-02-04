<?php

namespace Tests\Feature;

use App\Jobs\ProcessLiveWebinarRecording;
use ReflectionMethod;
use Tests\TestCase;

class ProcessLiveWebinarRecordingTest extends TestCase
{
    public function test_find_primary_recording_prioritizes_mp4_over_m4a(): void
    {
        $job = new ProcessLiveWebinarRecording(1);

        $recordings = collect([
            [
                'id' => 'audio-123',
                'file_type' => 'M4A',
                'status' => 'completed',
                'download_url' => 'https://zoom.us/recording/audio.m4a',
            ],
            [
                'id' => 'video-456',
                'file_type' => 'MP4',
                'status' => 'completed',
                'download_url' => 'https://zoom.us/recording/video.mp4',
            ],
        ]);

        $method = new ReflectionMethod($job, 'findPrimaryRecording');
        $method->setAccessible(true);

        $result = $method->invoke($job, $recordings);

        $this->assertNotNull($result);
        $this->assertEquals('video-456', $result['id']);
        $this->assertEquals('MP4', $result['file_type']);
    }

    public function test_find_primary_recording_falls_back_to_m4a_when_no_mp4(): void
    {
        $job = new ProcessLiveWebinarRecording(1);

        $recordings = collect([
            [
                'id' => 'audio-123',
                'file_type' => 'M4A',
                'status' => 'completed',
                'download_url' => 'https://zoom.us/recording/audio.m4a',
            ],
        ]);

        $method = new ReflectionMethod($job, 'findPrimaryRecording');
        $method->setAccessible(true);

        $result = $method->invoke($job, $recordings);

        $this->assertNotNull($result);
        $this->assertEquals('audio-123', $result['id']);
        $this->assertEquals('M4A', $result['file_type']);
    }

    public function test_find_primary_recording_returns_null_when_no_completed_recordings(): void
    {
        $job = new ProcessLiveWebinarRecording(1);

        $recordings = collect([
            [
                'id' => 'video-123',
                'file_type' => 'MP4',
                'status' => 'processing', // Not completed
                'download_url' => 'https://zoom.us/recording/video.mp4',
            ],
        ]);

        $method = new ReflectionMethod($job, 'findPrimaryRecording');
        $method->setAccessible(true);

        $result = $method->invoke($job, $recordings);

        $this->assertNull($result);
    }

    public function test_find_primary_recording_ignores_other_file_types(): void
    {
        $job = new ProcessLiveWebinarRecording(1);

        $recordings = collect([
            [
                'id' => 'chat-123',
                'file_type' => 'CHAT',
                'status' => 'completed',
            ],
            [
                'id' => 'transcript-456',
                'file_type' => 'VTT',
                'status' => 'completed',
            ],
        ]);

        $method = new ReflectionMethod($job, 'findPrimaryRecording');
        $method->setAccessible(true);

        $result = $method->invoke($job, $recordings);

        $this->assertNull($result);
    }

    public function test_job_timeout_is_set_for_large_files(): void
    {
        $job = new ProcessLiveWebinarRecording(1);

        $this->assertEquals(7200, $job->timeout);
    }

    public function test_job_tries_is5(): void
    {
        $job = new ProcessLiveWebinarRecording(1);

        $this->assertEquals(5, $job->tries);
    }
}
