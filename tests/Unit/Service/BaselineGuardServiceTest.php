<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Tests\Unit\Service;

use Leovie\PhpstanBaselineGuard\Neon\NeonDecoder;
use Leovie\PhpstanBaselineGuard\Parser\BaselineParser;
use Leovie\PhpstanBaselineGuard\Result\BaselineGuardResult;
use Leovie\PhpstanBaselineGuard\Result\ExpectedIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Result\FewerIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Result\NoMaxIgnoredErrorsGivenResult;
use Leovie\PhpstanBaselineGuard\Result\TooManyIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Service\BaselineGuardService;
use PHPUnit\Framework\TestCase;

class BaselineGuardServiceTest extends TestCase
{
    /** @dataProvider guardProvider */
    public function testGuard(BaselineGuardResult $expected, ?int $maxIgnoredErrors, int $countOfIgnoredErrors): void
    {
        $neonDecoder = $this->createMock(NeonDecoder::class);
        $neonDecoder->method('decodeFile')->willReturn([]);

        $baselineParser = $this->createMock(BaselineParser::class);
        $baselineParser->method('countIgnoredErrors')->willReturn($countOfIgnoredErrors);

        $baselineGuardService = new BaselineGuardService($neonDecoder, $baselineParser);

        self::assertEquals($expected, $baselineGuardService->guard('', $maxIgnoredErrors));
    }

    public function guardProvider(): array
    {
        return [
            'NoMaxIgnoredErrorsGiven' => [
                'expected' => new NoMaxIgnoredErrorsGivenResult(5),
                'maxIgnoredErrors' => null,
                'countOfIgnoredErrors' => 5,
            ],
            'FewerIgnoredErrorsResult' => [
                'expected' => new FewerIgnoredErrorsResult(5),
                'maxIgnoredErrors' => 10,
                'countOfIgnoredErrors' => 5,
            ],
            'TooManyIgnoredErrors' => [
                'expected' => new TooManyIgnoredErrorsResult(10),
                'maxIgnoredErrors' => 5,
                'countOfIgnoredErrors' => 10,
            ],
            'ExpectedIgnoredErrors' => [
                'expected' => new ExpectedIgnoredErrorsResult(5),
                'maxIgnoredErrors' => 5,
                'countOfIgnoredErrors' => 5,
            ],
        ];
    }
}