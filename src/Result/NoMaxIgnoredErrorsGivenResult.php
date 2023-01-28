<?php

namespace Leovie\PhpstanBaselineGuard\Result;

class NoMaxIgnoredErrorsGivenResult implements BaselineGuardResult
{
    public function __construct(
        private readonly int $countOfIgnoredErrors
    ) {}

    public function getCountOfIgnoredErrors(): int
    {
        return $this->countOfIgnoredErrors;
    }
}