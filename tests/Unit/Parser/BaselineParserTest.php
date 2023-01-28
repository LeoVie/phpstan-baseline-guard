<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Tests\Unit\Parser;

use Leovie\PhpstanBaselineGuard\Parser\BaselineParser;
use PHPUnit\Framework\TestCase;

class BaselineParserTest extends TestCase
{
    /** @dataProvider countIgnoredErrorsProvider */
    public function testCountIgnoredErrors(int $expected, array $baseline): void
    {
        self::assertSame($expected, (new BaselineParser())->countIgnoredErrors($baseline));
    }

    public function countIgnoredErrorsProvider(): array
    {
        return [
            'empty baseline' => [
                'expected' => 0,
                'baseline' => [
                    'parameters' => [
                        'ignoreErrors' => null
                    ]
                ]
            ],
            'non empty baseline' => [
                'expected' => 2,
                'baseline' => [
                    'parameters' => [
                        'ignoreErrors' => [
                            [
                                'message' => 'm1',
                            ],
                            [
                                'message' => 'm2',
                            ],
                        ]
                    ]
                ]
            ],
        ];
    }
}