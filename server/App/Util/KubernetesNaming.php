<?php

declare(strict_types=1);

namespace App\Util;

abstract class KubernetesNaming
{
    public static function simplifyObjectName(string $fullObjectName): string
    {
        return \preg_replace('|^.+/|', '', $fullObjectName);
    }
}
