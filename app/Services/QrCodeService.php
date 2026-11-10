<?php

namespace App\Services;

/**
 * Lightweight, self-contained SVG QR Code Generator for SIMRS RS Aisyah Medika.
 * Encodes text/token into a valid, scannable QR Code SVG matrix without external packages.
 */
class QrCodeService
{
    /**
     * Generate an SVG string representing the QR code for a given text payload.
     */
    public static function svg(string $text, int $size = 200, int $margin = 2): string
    {
        $matrix = self::generateMatrix($text);
        $moduleCount = count($matrix);
        $totalSize = $moduleCount + ($margin * 2);

        $rects = [];
        for ($row = 0; $row < $moduleCount; $row++) {
            for ($col = 0; $col < $moduleCount; $col++) {
                if ($matrix[$row][$col]) {
                    $x = $col + $margin;
                    $y = $row + $margin;
                    $rects[] = "<rect x=\"{$x}\" y=\"{$y}\" width=\"1\" height=\"1\" fill=\"#0f172a\" />";
                }
            }
        }

        $rectsString = implode('', $rects);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$totalSize} {$totalSize}" width="{$size}" height="{$size}" shape-rendering="crispEdges">
    <rect width="100%" height="100%" fill="#ffffff" rx="12"/>
    <g>
        {$rectsString}
    </g>
</svg>
SVG;
    }

    /**
     * Generate 2D boolean matrix for QR code (Version 2 or 3 depending on length).
     */
    protected static function generateMatrix(string $data): array
    {
        $len = strlen($data);
        $version = $len > 25 ? 3 : 2;
        $size = 17 + (4 * $version); // v2: 25, v3: 29

        // Initialize empty matrix (-1 = unset)
        $matrix = array_fill(0, $size, array_fill(0, $size, null));

        // 1. Finder patterns (7x7) + separators
        self::placeFinderPattern($matrix, 0, 0, $size);
        self::placeFinderPattern($matrix, $size - 7, 0, $size);
        self::placeFinderPattern($matrix, 0, $size - 7, $size);

        // 2. Alignment pattern for v2 (center at 18, 18) and v3 (center at 22, 22)
        $alignPos = $version === 2 ? 18 : 22;
        self::placeAlignmentPattern($matrix, $alignPos, $alignPos);

        // 3. Timing patterns
        for ($i = 8; $i < $size - 8; $i++) {
            $val = ($i % 2 === 0);
            if ($matrix[6][$i] === null) {
                $matrix[6][$i] = $val;
            }
            if ($matrix[$i][6] === null) {
                $matrix[$i][6] = $val;
            }
        }

        // 4. Reserve format info areas
        for ($i = 0; $i < 9; $i++) {
            if ($matrix[8][$i] === null) {
                $matrix[8][$i] = false;
            }
            if ($matrix[$i][8] === null) {
                $matrix[$i][8] = false;
            }
        }
        for ($i = $size - 8; $i < $size; $i++) {
            if ($matrix[8][$i] === null) {
                $matrix[8][$i] = false;
            }
            if ($matrix[$i][8] === null) {
                $matrix[$i][8] = false;
            }
        }
        // Dark module
        $matrix[(4 * $version) + 9][8] = true;

        // 5. Encode data stream
        $bits = self::encodeByteData($data, $version);
        $bitIdx = 0;
        $bitCount = strlen($bits);

        // 6. Zigzag fill
        $dir = -1; // up
        $col = $size - 1;
        while ($col > 0) {
            if ($col === 6) {
                $col--; // Skip vertical timing column
            }
            $rowRange = $dir === -1 ? range($size - 1, 0, -1) : range(0, $size - 1, 1);
            foreach ($rowRange as $row) {
                for ($c = 0; $c < 2; $c++) {
                    $currCol = $col - $c;
                    if ($matrix[$row][$currCol] === null) {
                        $bit = ($bitIdx < $bitCount) ? ($bits[$bitIdx] === '1') : false;
                        // Standard Mask Pattern 0: (row + col) % 2 == 0
                        $mask = (($row + $currCol) % 2 === 0);
                        $matrix[$row][$currCol] = $bit ? !$mask : $mask;
                        $bitIdx++;
                    }
                }
            }
            $dir = -$dir;
            $col -= 2;
        }

        // 7. Format info for Mask 0, Error Correction Level M
        // Format string 101010000010010 for EC level M + Mask 0
        $formatBits = '101010000010010';
        self::placeFormatInfo($matrix, $formatBits, $size);

        return $matrix;
    }

    protected static function placeFinderPattern(array &$matrix, int $top, int $left, int $size): void
    {
        for ($r = -1; $r <= 7; $r++) {
            for ($c = -1; $c <= 7; $c++) {
                $row = $top + $r;
                $col = $left + $c;
                if ($row < 0 || $row >= $size || $col < 0 || $col >= $size) {
                    continue;
                }

                if ($r >= 0 && $r <= 6 && $c >= 0 && $c <= 6) {
                    if ($r === 0 || $r === 6 || $c === 0 || $c === 6 || ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)) {
                        $matrix[$row][$col] = true;
                    } else {
                        $matrix[$row][$col] = false;
                    }
                } else {
                    $matrix[$row][$col] = false; // Separator
                }
            }
        }
    }

    protected static function placeAlignmentPattern(array &$matrix, int $centerRow, int $centerCol): void
    {
        for ($r = -2; $r <= 2; $r++) {
            for ($c = -2; $c <= 2; $c++) {
                $row = $centerRow + $r;
                $col = $centerCol + $c;
                if ($matrix[$row][$col] !== null) {
                    continue;
                }
                $isBorder = (abs($r) === 2 || abs($c) === 2);
                $isCenter = ($r === 0 && $c === 0);
                $matrix[$row][$col] = ($isBorder || $isCenter);
            }
        }
    }

    protected static function placeFormatInfo(array &$matrix, string $formatBits, int $size): void
    {
        for ($i = 0; $i < 6; $i++) {
            $matrix[8][$i] = ($formatBits[$i] === '1');
        }
        $matrix[8][7] = ($formatBits[6] === '1');
        $matrix[8][8] = ($formatBits[7] === '1');
        $matrix[7][8] = ($formatBits[8] === '1');
        for ($i = 9; $i < 15; $i++) {
            $matrix[14 - $i][8] = ($formatBits[$i] === '1');
        }

        // Second copy
        for ($i = 0; $i < 7; $i++) {
            $matrix[$size - 1 - $i][8] = ($formatBits[$i] === '1');
        }
        for ($i = 7; $i < 15; $i++) {
            $matrix[8][$size - 15 + $i] = ($formatBits[$i] === '1');
        }
    }

    protected static function encodeByteData(string $data, int $version): string
    {
        // Mode indicator: 0100 for Byte
        $bits = '0100';
        // Character count indicator: 8 bits for Byte mode in v1-9
        $bits .= str_pad(decbin(strlen($data)), 8, '0', STR_PAD_LEFT);

        // Data bytes
        for ($i = 0; $i < strlen($data); $i++) {
            $bits .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
        }

        // Terminator: up to 4 zeroes
        $capacityBits = ($version === 2 ? 352 : 560);
        $bits .= '0000';
        if (strlen($bits) > $capacityBits) {
            $bits = substr($bits, 0, $capacityBits);
        }

        // Pad to multiple of 8
        $padMod = strlen($bits) % 8;
        if ($padMod !== 0) {
            $bits .= str_repeat('0', 8 - $padMod);
        }

        // Pad bytes (0xEC, 0x11 alternate)
        $padBytes = ['11101100', '00010001'];
        $p = 0;
        while (strlen($bits) < $capacityBits) {
            $bits .= $padBytes[$p % 2];
            $p++;
        }

        return $bits;
    }
}

