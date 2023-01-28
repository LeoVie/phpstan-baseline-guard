<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Neon;

use Nette\Neon\Exception;
use Nette\Neon\Neon;

class NeonDecoder
{
    /** @throws Exception */
    public function decodeFile(string $path): mixed
    {
        return Neon::decodeFile($path);
    }
}