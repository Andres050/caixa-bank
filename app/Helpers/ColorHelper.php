<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Converts a hexadecimal color code to its RGB representation.
     *
     * @param string $hex The hexadecimal color code (with or without the '#' symbol).
     * @return array An array containing the red, green, and blue components as integers.
     */
    public static function hexToRgb(string $hex): array
    {
        // Remove the '#' symbol if it exists
        $hex = ltrim($hex, '#');

        // Convert the hex color to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return [$r, $g, $b];
    }
}
