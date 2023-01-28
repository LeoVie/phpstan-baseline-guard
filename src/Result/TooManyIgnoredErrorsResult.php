<?php

namespace Leovie\PhpstanBaselineGuard\Result;

readonly class TooManyIgnoredErrorsResult implements BaselineGuardResult
{
    public function __construct(
        private int $countOfIgnoredErrors
    ) {}

    public function getCountOfIgnoredErrors(): int
    {
        return $this->countOfIgnoredErrors;
    }
}