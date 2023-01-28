<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Parser;

/**
 * @phpstan-type Baseline = array{
 *     parameters: array{
 *          ignoreErrors: null|array<mixed>
 *     }
 * }
 */
class BaselineParser
{
    /** @phpstan-param Baseline $baseline */
    public function countIgnoredErrors(array $baseline): int
    {
        $ignoreErrors = $baseline['parameters']['ignoreErrors'];

        return $ignoreErrors === null
            ? 0
            : count($ignoreErrors);
    }
}