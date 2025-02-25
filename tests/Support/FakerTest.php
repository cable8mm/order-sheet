<?php

namespace Cable8mm\OrderSheet\Tests\Support;

use Cable8mm\OrderSheet\Support\Faker;
use PHPUnit\Framework\TestCase;

final class FakerTest extends TestCase
{
    public function test_date_time(): void
    {
        $value = Faker::make()->dateTime();

        $this->assertMatchesRegularExpression('/^.+(오전|오후).+$/u', $value);
    }

    public function test_product_name(): void
    {
        $value = Faker::shared()->productName();

        $this->assertIsString($value);
    }

    public function test_is_name_korean(): void
    {
        $value = Faker::shared()->name();

        $this->assertMatchesRegularExpression('/[\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', $value);
    }
}
