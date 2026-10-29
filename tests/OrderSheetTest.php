<?php

namespace Cable8mm\OrderSheet\Tests;

use Cable8mm\OrderSheet\Enums\OrderSheetType;
use Cable8mm\OrderSheet\OrderSheet;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class OrderSheetTest extends TestCase
{
    private const PLAYAUTO_HEADER = [
        '주문고유번호', '판매사이트명', '판매사이트코드', '판매자ID', '주문수집자ID',
        '수집일', '주문일', '결제일', '상태변경일', '배송일', '상태', '판매사이트 주문번호',
        '판매사이트 상품코드', '상품명', '상품명-홍보문구', '주문선택사항', '주문선택사항금액',
        '추가구매옵션', '추가구매옵션금액', '원가', '공급가', '판매가', '총판매가(묶음후)',
        '주문수량', '총주문수량(묶음후)', '배송방법(원본)', '배송방법(묶음/조건적용후)',
        '배송방법 자동적용 사유', '배송비금액', '총배송비금액(묶음후)', '구매자ID', '구매자명',
        '구매자전화번호', '구매자휴대폰번호', '수령자명', '수령자전화번호', '수령자휴대폰번호',
        '배송지우편번호', '배송지주소', '배송메세지', '배송사명', '운송장번호', '마스터상품코드',
        '주의메세지', '판매자상품코드',
    ];

    public function test_header_row_precedes_data_rows(): void
    {
        $data = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(3)
            ->header()
            ->toArray();

        $this->assertCount(4, $data);
        $this->assertSame(self::PLAYAUTO_HEADER, $data[0]);

        foreach (array_slice($data, 1) as $row) {
            $this->assertCount(count($data[0]), $row);
        }
    }

    public function test_header_can_be_turned_off(): void
    {
        $data = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(2)
            ->header()
            ->header(false)
            ->toArray();

        $this->assertCount(2, $data);
        $this->assertNotSame(self::PLAYAUTO_HEADER, $data[0]);
    }

    public function test_it_creates_an_order_sheet_builder(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType);

        $this->assertInstanceOf(OrderSheet::class, $orderSheet);
    }

    public function test_setters_return_the_same_builder(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType);

        $this->assertSame($orderSheet, $orderSheet->count(10));
        $this->assertSame($orderSheet, $orderSheet->bundle(2));
        $this->assertSame($orderSheet, $orderSheet->state(['상태' => '송장입력']));
        $this->assertSame($orderSheet, $orderSheet->header());
        $this->assertSame($orderSheet, $orderSheet->path('dist'));
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

    public function test_it_throws_exception_for_negative_count(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Count must be at least 1');

        OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(-1);
    }

    public function test_it_generates_one_row_by_default(): void
    {
        $data = OrderSheet::of(OrderSheetType::PlayautoType)->toArray();

        $this->assertCount(1, $data);
    }

    public function test_it_generates_count_rows_of_45_cells(): void
    {
        foreach ([1, 2, 10] as $count) {
            $data = OrderSheet::of(OrderSheetType::PlayautoType)
                ->count($count)
                ->toArray();

            $this->assertCount($count, $data);

            foreach ($data as $row) {
                $this->assertCount(45, $row);
            }
        }
    }

    public function test_each_export_generates_new_rows(): void
    {
        $orderSheet = OrderSheet::of(OrderSheetType::PlayautoType)->count(3);

        $this->assertNotEquals($orderSheet->toArray(), $orderSheet->toArray());
    }

    public function test_it_throws_exception_for_invalid_bundle_size(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bundle size must be at least 1');

        OrderSheet::of(OrderSheetType::PlayautoType)
            ->bundle(0);
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

    public function test_it_can_generate_multiple_lines_for_each_order(): void
    {
        $data = OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(3)
            ->bundle(2)
            ->toArray();

        $this->assertCount(3, $data);
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

    public function test_state_values_fill_their_columns_in_every_row(): void
    {
        $rows = $this->keyedRows(OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(5)
            ->state([
                '상태' => '배송완료',
                '구매자명' => '홍길동',
                '주문수량' => 5,
            ]));

        $this->assertCount(5, $rows);

        foreach ($rows as $row) {
            $this->assertSame('배송완료', $row['상태']);
            $this->assertSame('홍길동', $row['구매자명']);
            $this->assertSame(5, $row['주문수량']);
            $this->assertGreaterThanOrEqual(1000, $row['판매가']);
            $this->assertLessThanOrEqual(100000, $row['판매가']);
            $this->assertSame($row['구매자전화번호'], $row['수령자전화번호']);
            $this->assertSame($row['마스터상품코드'], $row['판매자상품코드']);
        }
    }

    public function test_state_replaces_previous_state(): void
    {
        $rows = $this->keyedRows(OrderSheet::of(OrderSheetType::PlayautoType)
            ->state(['상태' => '배송완료'])
            ->state(['구매자명' => '홍길동']));

        $this->assertSame('송장입력', $rows[0]['상태']);
        $this->assertSame('홍길동', $rows[0]['구매자명']);
    }

    public function test_unknown_state_key_warns_and_is_ignored(): void
    {
        $warnings = [];

        set_error_handler(function (int $level, string $message) use (&$warnings) {
            $warnings[] = [$level, $message];

            return true;
        });

        try {
            $data = OrderSheet::of(OrderSheetType::PlayautoType)
                ->count(2)
                ->state(['없는컬럼' => 'x'])
                ->toArray();
        } finally {
            restore_error_handler();
        }

        $this->assertNotEmpty($warnings);

        foreach ($warnings as [$level, $message]) {
            $this->assertSame(E_USER_WARNING, $level);
            $this->assertSame('State key "없는컬럼" does not exist in definition and will be ignored', $message);
        }

        $this->assertCount(2, $data);

        foreach ($data as $row) {
            $this->assertCount(45, $row);
            $this->assertNotContains('x', $row);
        }
    }

    public function test_it_throws_exception_for_negative_bundle_size(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bundle size must be at least 1');

        OrderSheet::of(OrderSheetType::PlayautoType)
            ->bundle(-1);
    }

    public function test_last_order_holds_the_remaining_rows(): void
    {
        $rows = $this->keyedRows(OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(3)
            ->bundle(2));

        $this->assertCount(3, $rows);
        $this->assertSame($rows[0]['주문고유번호'], $rows[1]['주문고유번호']);
        $this->assertNotSame($rows[1]['주문고유번호'], $rows[2]['주문고유번호']);
    }

    public function test_bundled_orders_share_order_fields_and_totals(): void
    {
        $orderFields = [
            '주문고유번호', '판매사이트명', '판매사이트코드', '판매자ID', '주문수집자ID',
            '수집일', '주문일', '결제일', '상태변경일', '배송일', '상태', '판매사이트 주문번호',
            '배송방법(원본)', '배송방법(묶음/조건적용후)', '배송방법 자동적용 사유', '배송비금액',
            '총배송비금액(묶음후)', '구매자ID', '구매자명', '구매자전화번호', '구매자휴대폰번호',
            '수령자명', '수령자전화번호', '수령자휴대폰번호', '배송지우편번호', '배송지주소',
            '배송메세지', '배송사명', '운송장번호',
        ];

        $rows = $this->keyedRows(OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(6)
            ->bundle(3));

        foreach (array_chunk($rows, 3) as $order) {
            $quantity = array_sum(array_column($order, '주문수량'));
            $price = array_sum(array_map(fn (array $row) => $row['판매가'] * $row['주문수량'], $order));

            foreach ($order as $row) {
                foreach ($orderFields as $field) {
                    $this->assertSame($order[0][$field], $row[$field]);
                }

                $this->assertSame($quantity, $row['총주문수량(묶음후)']);
                $this->assertSame($price, $row['총판매가(묶음후)']);
            }
        }
    }

    public function test_single_line_orders_total_their_own_values(): void
    {
        $rows = $this->keyedRows(OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(5)
            ->bundle(1));

        $this->assertNotSame($rows[0]['주문고유번호'], $rows[1]['주문고유번호']);

        foreach ($rows as $row) {
            $this->assertSame($row['주문수량'], $row['총주문수량(묶음후)']);
            $this->assertSame($row['판매가'] * $row['주문수량'], $row['총판매가(묶음후)']);
        }
    }

    public function test_bundle_totals_use_state_values(): void
    {
        $rows = $this->keyedRows(OrderSheet::of(OrderSheetType::PlayautoType)
            ->state(['주문수량' => 5]));

        $this->assertSame(5, $rows[0]['총주문수량(묶음후)']);
    }

    public function test_bundle_totals_replace_state_values(): void
    {
        $rows = $this->keyedRows(OrderSheet::of(OrderSheetType::PlayautoType)
            ->count(4)
            ->bundle(2)
            ->state(['총주문수량(묶음후)' => 99]));

        foreach (array_chunk($rows, 2) as $order) {
            foreach ($order as $row) {
                $this->assertSame(array_sum(array_column($order, '주문수량')), $row['총주문수량(묶음후)']);
            }
        }
    }

    /**
     * Generate rows keyed by column name.
     *
     * @return array<int, array<string, mixed>>
     */
    private function keyedRows(OrderSheet $orderSheet): array
    {
        return array_map(
            fn (array $row) => array_combine(self::PLAYAUTO_HEADER, $row),
            $orderSheet->toArray()
        );
    }
}
