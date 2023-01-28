<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Result;

interface BaselineGuardResult
{
    public function getCountOfIgnoredErrors(): int;
}