<?php

namespace Anacona16\Bundle\ImageCropBundle\Util;

use Symfony\Component\HttpKernel\Kernel;

final class LegacyFormHelper
{
    const MINIMUM_MAJOR_VERSION = 3;

    /**
     * Check whether to use legacy form behaviour from Symfony <3.0.
     *
     * @param int $majorVersion Major version
     *
     * @static
     *
     * @return bool
     */
    public static function isLegacy($majorVersion = Kernel::MAJOR_VERSION)
    {
        return $majorVersion < self::MINIMUM_MAJOR_VERSION;
    }
}
