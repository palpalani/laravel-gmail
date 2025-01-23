<?php

declare(strict_types=1);

use Dacastro4\LaravelGmail\Services\Message\Mail;
use Illuminate\Container\Container;
use Illuminate\Mail\Markdown;
use Tests\TestCase;

final class LaravelGmailTest extends TestCase
{
    /** @test */
    public function test_markdown_method(): void
    {
        // mocks
        $mocked_markdown = Mockery::mock(Markdown::class);
        Container::getInstance()->instance(Markdown::class, $mocked_markdown);

        // expectations
        $mocked_markdown->shouldReceive('theme')->once()->with(config('mail.markdown.theme'));
        $mocked_markdown->shouldReceive('render')->once()->with(
            'sample-markdown',
            ['url' => 'https://www.google.com'],
        );

        // trigger
        (new Mail())->markdown(
            'sample-markdown',
            ['url' => 'https://www.google.com'],
        );
    }
}
