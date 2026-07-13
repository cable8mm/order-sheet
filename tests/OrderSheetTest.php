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

    public function test_it_can_convert_to_string(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType);

        $this->assertEquals('PlayautoType', (string) $orderSheet);
    }

    public function test_it_export_to_array(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(1)
            ->header()
            ->toArray();

        $this->assertIsArray($orderSheet);
    }

    public function test_it_export_to_array_with_state(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(1)
            ->state(['상태' => '송장입력'])
            ->toArray();

        $this->assertIsArray($orderSheet);
        $this->assertCount(1, $orderSheet);
    }

    public function test_it_export_to_csv(): void
    {
        $csv = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(1)
            ->csv();

        $rows = str_getcsv($csv, "\n");

        $this->assertCount(1, $rows);
        $this->assertIsString($csv);
    }

    public function test_it_export_to_csv_with_header(): void
    {
        $csv = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(1)
            ->header()
            ->csv();

        $rows = str_getcsv($csv, "\n");

        $this->assertCount(2, $rows);
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

    public function test_it_throws_exception_for_invalid_count(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Count must be at least 1');

        OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(0);
    }

    public function test_it_throws_exception_for_nonexistent_directory(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Directory "/nonexistent/path" does not exist');

        OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(10)
            ->path('/nonexistent/path')
            ->xlsx('test.xlsx');
    }

    public function test_csv_contains_correct_number_of_rows(): void
    {
        $csv = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(5)
            ->csv();

        $rows = str_getcsv($csv, "\n");
        $this->assertCount(5, $rows);
    }

    public function test_csv_with_header_contains_header_and_data_rows(): void
    {
        $csv = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(3)
            ->header()
            ->csv();

        $rows = str_getcsv($csv, "\n");
        $this->assertCount(4, $rows); // 1 header + 3 data rows

        // Verify first row is header (contains column names)
        $header = str_getcsv($rows[0], ',');
        $this->assertContains('주문고유번호', $header);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $data = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(2)
            ->toArray();

        $this->assertCount(2, $data);
        $this->assertIsArray($data[0]);
        $this->assertIsArray($data[1]);
    }

    public function test_state_overrides_multiple_fields(): void
    {
        $data = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(1)
            ->state([
                '상태' => '배송완료',
                '구매자명' => '홍길동',
                '주문수량' => 5,
            ])
            ->toArray();

        $this->assertCount(1, $data);
        $this->assertContains('배송완료', $data[0]);
        $this->assertContains('홍길동', $data[0]);
        $this->assertContains(5, $data[0]);
    }
}
