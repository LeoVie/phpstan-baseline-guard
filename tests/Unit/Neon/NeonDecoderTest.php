<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Tests\Unit\Neon;

use Leovie\PhpstanBaselineGuard\Neon\NeonDecoder;
use PHPUnit\Framework\TestCase;

class NeonDecoderTest extends TestCase
{
    /** @dataProvider decodeFileProvider */
    public function testDecodeFile(array $expected, string $path): void
    {
        self::assertSame($expected, (new NeonDecoder())->decodeFile($path));
    }

    public function decodeFileProvider(): array
    {
        $s = DIRECTORY_SEPARATOR;
        return [
            'empty' => [
                'expected' => [
                    'parameters' => [
                        'ignoreErrors' => null,
                    ],
                ],
                'path' => __DIR__ . "${s}..${s}..${s}_testdata${s}baseline${s}empty.neon"
            ],
            'non empty' => [
                'expected' => [
                    'parameters' => [
                        'ignoreErrors' => [
                            [
                                'message' => '#^Only numeric types are allowed in pre\\-decrement, bool\\|float\\|int\\|string\\|null given\\.$#',
                                'count' => 1,
                                'path' => 'src/Analyser/Scope.php',
                            ],
                            [
                                'message' => '#^Anonymous function has an unused use \\$container\\.$#',
                                'count' => 2,
                                'path' => 'src/Command/CommandHelper.php',
                            ],
                        ],
                    ],
                ],
                'path' => __DIR__ . "${s}..${s}..${s}_testdata${s}baseline${s}non_empty.neon"
            ],
        ];
    }
}