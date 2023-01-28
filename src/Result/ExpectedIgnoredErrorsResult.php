<?php

namespace Leovie\PhpstanBaselineGuard\Result;

class ExpectedIgnoredErrorsResult implements BaselineGuardResult
{
    public function __construct(
        private readonly int $countOfIgnoredErrors
    ) {}

    public function getCountOfIgnoredErrors(): int
    {
        return $this->countOfIgnoredErrors;
    }
}