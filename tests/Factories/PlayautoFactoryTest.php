<?php

namespace Cable8mm\OrderSheet\Tests\Factories;

use Cable8mm\OrderSheet\Factories\PlayautoFactory;
use PHPUnit\Framework\TestCase;

final class PlayautoFactoryTest extends TestCase
{
    public function test_it_must_have_all_fields(): void
    {
        $factoryFields = PlayautoFactory::make()->definition();

        $this->assertEquals(45, count($factoryFields));
    }

    public function test_it_must_be_fit_with_a_definition_type(): void
    {
        $factoryFields = PlayautoFactory::make()->definition();

        $this->assertIsArray($factoryFields);
    }

    public function test_it_create_basically(): void
    {
        $playautoFactory = PlayautoFactory::make()->create();

        $this->assertEquals(1, count($playautoFactory));
    }

    public function test_it_create_with_state(): void
    {
        $keys = array_keys(PlayautoFactory::make()->definition());
        $statusIndex = array_search('상태', $keys);

        $playautoFactories = PlayautoFactory::make()->state(['상태' => '송장입력'])->count(2)->create();

        $this->assertIsArray($playautoFactories);
        $this->assertCount(2, $playautoFactories);
        $this->assertEquals('송장입력', $playautoFactories[0][$statusIndex]);
        $this->assertEquals('송장입력', $playautoFactories[1][$statusIndex]);
    }
}
