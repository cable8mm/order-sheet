<?php

namespace Cable8mm\OrderSheet\Tests;

use Cable8mm\OrderSheet\Enums\OrderSheetType;
use Cable8mm\OrderSheet\OrderSheet;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class OrderSheetTest extends TestCase
{
    public function test_it_run_correctly(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(10)
            ->path('dist');

        $this->assertTrue(true);
    }

    public function test_path_exists(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->path(realpath(__DIR__.'/../dist'));

        $reflection = new ReflectionClass($orderSheet);

        $path = $reflection->getProperty('path');

        $path->setAccessible(true);

        $this->assertStringContainsString(DIRECTORY_SEPARATOR.'dist', $path->getValue($orderSheet));
    }

    public function test_it_export_to_array(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(1)
            ->header()
            ->toArray();

        $this->assertIsArray($orderSheet);
    }

    public function test_it_export_to_csv(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(1)
            ->csv();

        $this->expectNotToPerformAssertions(\ValueError::class);

        str_getcsv($orderSheet);
    }

    public function test_it_export_to_xlsx(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(10)
            ->path('dist')
            ->header()
            ->xlsx();

        $this->assertFileExists(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'dist'.DIRECTORY_SEPARATOR.'order_sheet.xlsx'));

        unlink(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'dist'.DIRECTORY_SEPARATOR.'order_sheet.xlsx'));
    }

    public function test_it_export_to_xlsx_with_naming(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(10)
            ->path('dist')
            ->header()
            ->xlsx('make_custom_name.xlsx');

        $this->assertFileExists(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'dist'.DIRECTORY_SEPARATOR.'make_custom_name.xlsx'));

        unlink(realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'dist'.DIRECTORY_SEPARATOR.'make_custom_name.xlsx'));
    }
}
