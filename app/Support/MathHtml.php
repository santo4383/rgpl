<?php

namespace App\Support;

class MathHtml
{
    /**
     * Convert editor HTML into a meaning-preserving plain-text representation.
     *
     * Formula blots become `$latex$`, superscripts become `^n` / `^{..}`,
     * subscripts become `_n` / `_{..}`, block elements become spaces and
     * HTML entities are decoded so Unicode math symbols are kept intact.
     */
    public static function toPlainText(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $text = preg_replace_callback(
            '/<span[^>]*class="[^"]*ql-formula[^"]*"[^>]*data-value="([^"]*)"[^>]*>.*?<\/span>(?:<\/span>)*/is',
            fn (array $m): string => '$'.html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8').'$',
            $html
        );

        // Formula spans where data-value precedes class
        $text = preg_replace_callback(
            '/<span[^>]*data-value="([^"]*)"[^>]*class="[^"]*ql-formula[^"]*"[^>]*>.*?<\/span>(?:<\/span>)*/is',
            fn (array $m): string => '$'.html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8').'$',
            $text
        );

        $text = preg_replace_callback('/<sup[^>]*>(.*?)<\/sup>/is', fn (array $m): string => self::script('^', $m[1]), $text);
        $text = preg_replace_callback('/<sub[^>]*>(.*?)<\/sub>/is', fn (array $m): string => self::script('_', $m[1]), $text);

        $text = preg_replace('/<(br|\/p|\/div|\/li|\/h[1-6])[^>]*>/i', ' ', $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{00A0}", ' ', $text);

        return trim(preg_replace('/[ \t\r\n]+/u', ' ', $text));
    }

    /**
     * Render a super/subscript marker, wrapping multi-character content in braces.
     */
    protected static function script(string $marker, string $inner): string
    {
        $inner = trim(strip_tags($inner));

        return mb_strlen($inner) === 1 ? $marker.$inner : $marker.'{'.$inner.'}';
    }
}
