<?php

declare(strict_types=1);

namespace AndyDefer\PhpServices\Enums;

/**
 * Normalization mode for text processing.
 */
enum NormalizationMode: string
{
    /**
     * No normalization applied.
     */
    case WITHOUT = 'without';

    /**
     * Apply normalization (accents removal, lowercasing, etc.).
     */
    case WITH_NORMALIZATION = 'with_normalization';
}
