<?php

namespace Tests\Unit;

use App\Support\MathHtml;
use PHPUnit\Framework\TestCase;

class MathHtmlTest extends TestCase
{
    public function test_formula_blots_become_latex_with_delimiters(): void
    {
        $html = '<p>Find <span class="ql-formula" data-value="\sqrt[3]{24389}">\sqrt[3]{24389}</span> = ?</p>';

        $this->assertSame('Find $\sqrt[3]{24389}$ = ?', MathHtml::toPlainText($html));
    }

    public function test_superscripts_and_subscripts_keep_their_meaning(): void
    {
        $html = '<p>2<sup>2</sup>×2<sup>3</sup> and x<sub>10</sub> and a<sup>-2/3</sup></p>';

        $this->assertSame('2^2×2^3 and x_{10} and a^{-2/3}', MathHtml::toPlainText($html));
    }

    public function test_unicode_math_symbols_and_entities_are_preserved(): void
    {
        $html = '<p>√(a + b)² ÷ ∛8 &lt; π &amp; x ≥ 0</p><p>Next</p>';

        $this->assertSame('√(a + b)² ÷ ∛8 < π & x ≥ 0 Next', MathHtml::toPlainText($html));
    }

    public function test_empty_input_returns_empty_string(): void
    {
        $this->assertSame('', MathHtml::toPlainText(null));
        $this->assertSame('', MathHtml::toPlainText('   '));
    }
}
