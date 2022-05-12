<?php

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Fields\Text;
use PHPUnit\Framework\TestCase;

class BaseDecorationTest extends TestCase
{
    public function testBasicTabDecoration()
    {
        $decoration = Tab::make('Tab', [
            Text::make('First name'),
            Text::make('Last name'),
        ]);

        $this->assertEquals('Tab', $decoration->label());
        $this->assertTrue($decoration->hasFields());
        $this->assertCount(2, $decoration->fields());
    }

    public function testBasicHeadingDecoartion()
    {
        $decoration = Heading::make('h1');

        $this->assertEquals('h1', $decoration->label());
    }
}