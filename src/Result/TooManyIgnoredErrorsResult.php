<?php

namespace Leovie\PhpstanBaselineGuard\Result;

class TooManyIgnoredErrorsResult implements BaselineGuardResult
{
    public function __construct(
        private readonly int $countOfIgnoredErrors
    ) {}

    public function getCountOfIgnoredErrors(): int
    {
        return $this->countOfIgnoredErrors;
    }
}