<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Service;

use Leovie\PhpstanBaselineGuard\Neon\NeonDecoder;
use Leovie\PhpstanBaselineGuard\Parser\BaselineParser;
use Leovie\PhpstanBaselineGuard\Result\BaselineGuardResult;
use Leovie\PhpstanBaselineGuard\Result\ExpectedIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Result\FewerIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Result\NoMaxIgnoredErrorsGivenResult;
use Leovie\PhpstanBaselineGuard\Result\TooManyIgnoredErrorsResult;

/** @phpstan-import-type Baseline from BaselineParser */
class BaselineGuardService
{
    public function __construct(
        private readonly NeonDecoder    $neonDecoder,
        private readonly BaselineParser $baselineParser,
    ) {}

    public function guard(string $baselinePath, ?int $maxIgnoredErrors): BaselineGuardResult
    {
        /** @var Baseline $baseline */
        $baseline = $this->neonDecoder->decodeFile($baselinePath);
        $countOfIgnoredErrors = $this->baselineParser->countIgnoredErrors($baseline);

        if ($maxIgnoredErrors === null) {
            return new NoMaxIgnoredErrorsGivenResult($countOfIgnoredErrors);
        }

        if ($countOfIgnoredErrors < $maxIgnoredErrors) {
            return new FewerIgnoredErrorsResult($countOfIgnoredErrors);
        }

        if ($countOfIgnoredErrors > $maxIgnoredErrors) {
            return new TooManyIgnoredErrorsResult($countOfIgnoredErrors);
        }

        return new ExpectedIgnoredErrorsResult($countOfIgnoredErrors);
    }
}