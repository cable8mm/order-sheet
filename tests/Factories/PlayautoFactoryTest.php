<?php

namespace Cable8mm\OrderSheet\Tests\Factories;

use Cable8mm\OrderSheet\Factories\PlayautoFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class PlayautoFactoryTest extends TestCase
{
    public function test_it_must_have_all_fields(): void
    {
        $factoryFields = PlayautoFactory::make()->definition();

        $this->assertSame(45, count($factoryFields));
        $this->assertSame(PlayautoFactory::make()->header(), array_keys($factoryFields));
    }

    public function test_it_has_a_stable_playauto_column_layout(): void
    {
        $this->assertSame([
            '주문고유번호', '판매사이트명', '판매사이트코드', '판매자ID', '주문수집자ID',
            '수집일', '주문일', '결제일', '상태변경일', '배송일', '상태', '판매사이트 주문번호',
            '판매사이트 상품코드', '상품명', '상품명-홍보문구', '주문선택사항', '주문선택사항금액',
            '추가구매옵션', '추가구매옵션금액', '원가', '공급가', '판매가', '총판매가(묶음후)',
            '주문수량', '총주문수량(묶음후)', '배송방법(원본)', '배송방법(묶음/조건적용후)',
            '배송방법 자동적용 사유', '배송비금액', '총배송비금액(묶음후)', '구매자ID', '구매자명',
            '구매자전화번호', '구매자휴대폰번호', '수령자명', '수령자전화번호', '수령자휴대폰번호',
            '배송지우편번호', '배송지주소', '배송메세지', '배송사명', '운송장번호', '마스터상품코드',
            '주의메세지', '판매자상품코드',
        ], PlayautoFactory::make()->header());
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

    public function test_it_generates_consistent_grouped_order_lines(): void
    {
        $factory = PlayautoFactory::make()->count(4)->bundle(2);
        $header = $factory->header();
        $rows = array_map(fn (array $row) => array_combine($header, $row), $factory->create());

        $this->assertCount(4, $rows);

        foreach (array_chunk($rows, 2) as $orderLines) {
            $firstLine = $orderLines[0];
            $expectedQuantity = array_sum(array_column($orderLines, '주문수량'));
            $expectedPrice = array_sum(array_map(
                fn (array $line) => $line['판매가'] * $line['주문수량'],
                $orderLines
            ));

            foreach ($orderLines as $line) {
                $this->assertSame($firstLine['주문고유번호'], $line['주문고유번호']);
                $this->assertSame($firstLine['판매사이트 주문번호'], $line['판매사이트 주문번호']);
                $this->assertSame($firstLine['구매자명'], $line['구매자명']);
                $this->assertSame($firstLine['수령자명'], $line['수령자명']);
                $this->assertSame($firstLine['운송장번호'], $line['운송장번호']);
                $this->assertSame($expectedQuantity, $line['총주문수량(묶음후)']);
                $this->assertSame($expectedPrice, $line['총판매가(묶음후)']);
                $this->assertGreaterThanOrEqual($line['공급가'], $line['원가']);
                $this->assertSame(
                    $line['배송방법(원본)'] === '무료배송' ? 0 : 3000,
                    $line['배송비금액']
                );
            }
        }
    }

    public function test_column_41_is_named_invoice_number(): void
    {
        $factory = PlayautoFactory::make()->count(3);
        $rows = [$factory->header(), ...$factory->create()];

        $this->assertSame('운송장번호', $rows[0][41]);

        foreach ($rows as $row) {
            $this->assertNotContains('송장번호', $row);
        }
    }

    public function test_it_generates_chronological_order_dates(): void
    {
        $factory = PlayautoFactory::make();
        $row = array_combine($factory->header(), $factory->create()[0]);

        $this->assertLessThanOrEqual(
            $this->dateTime($row['수집일']),
            $this->dateTime($row['주문일'])
        );
        $this->assertLessThanOrEqual(
            $this->dateTime($row['결제일']),
            $this->dateTime($row['수집일'])
        );
        $this->assertLessThanOrEqual(
            $this->dateTime($row['상태변경일']),
            $this->dateTime($row['결제일'])
        );
        $this->assertLessThanOrEqual(
            $this->dateTime($row['배송일']),
            $this->dateTime($row['상태변경일'])
        );
    }

    public function test_it_generates_identifier_values(): void
    {
        $extraOptions = [];

        foreach ($this->rows(100) as $row) {
            $this->assertMatchesRegularExpression('/^[1-9]\d{11}$/', (string) $row['주문고유번호']);
            $this->assertContains($row['판매사이트명'], ['티몬', '고도몰5', '카페24(신)', '위메프2.0', '롯데백화점', '옥션']);
            $this->assertMatchesRegularExpression('/^[AB][1-9]\d{2}$/', $row['판매사이트코드']);

            foreach (['판매자ID', '주문수집자ID', '구매자ID'] as $column) {
                $this->assertMatchesRegularExpression('/^\S+$/u', $row[$column]);
            }

            $this->assertSame('송장입력', $row['상태']);
            $this->assertMatchesRegularExpression('/^2\d{9}$/', $row['판매사이트 주문번호']);
            $this->assertMatchesRegularExpression('/^[1-9]\d{9}$/', (string) $row['판매사이트 상품코드']);
            $this->assertIsString($row['상품명']);
            $this->assertNotSame('', $row['상품명']);

            foreach (['상품명-홍보문구', '주문선택사항', '배송방법 자동적용 사유', '주의메세지'] as $column) {
                $this->assertEmpty($row[$column]);
            }

            $this->assertSame(0, $row['주문선택사항금액']);
            $this->assertSame(0, $row['추가구매옵션금액']);

            if (! empty($row['추가구매옵션'])) {
                $extraOptions[$row['추가구매옵션']] = true;
            }

            $this->assertIsString($row['배송사명']);
            $this->assertNotSame('', $row['배송사명']);
            $this->assertIsInt($row['운송장번호']);
            $this->assertGreaterThanOrEqual(1, $row['운송장번호']);
            $this->assertLessThanOrEqual(1000000000, $row['운송장번호']);
            $this->assertIsInt($row['마스터상품코드']);
            $this->assertGreaterThanOrEqual(1, $row['마스터상품코드']);
            $this->assertLessThanOrEqual(10, $row['마스터상품코드']);
            $this->assertSame($row['마스터상품코드'], $row['판매자상품코드']);
        }

        $this->assertLessThanOrEqual(1, count($extraOptions));
    }

    public function test_it_generates_date_time_values(): void
    {
        $before = new DateTimeImmutable;
        $rows = $this->rows(100);
        $after = new DateTimeImmutable;

        $intervals = [
            ['주문일', '수집일', 60, 3600],
            ['수집일', '결제일', 60, 1800],
            ['결제일', '상태변경일', 60, 7200],
            ['상태변경일', '배송일', 3600, 86400],
        ];

        foreach ($rows as $row) {
            foreach (['수집일', '주문일', '결제일', '상태변경일', '배송일'] as $column) {
                $this->assertMatchesRegularExpression(
                    '/^\d{4}-\d{2}-\d{2} (오전|오후) (0[1-9]|1[0-2]):[0-5]\d:[0-5]\d$/u',
                    $row[$column]
                );
                $this->assertSame(
                    $row[$column],
                    str_replace(['AM', 'PM'], ['오전', '오후'], $this->dateTime($row[$column])->format('Y-m-d A h:i:s'))
                );
            }

            $orderedAt = $this->dateTime($row['주문일'])->getTimestamp();
            $this->assertGreaterThanOrEqual($before->modify('-30 days')->getTimestamp() - 1, $orderedAt);
            $this->assertLessThanOrEqual($after->modify('-1 day')->getTimestamp(), $orderedAt);

            foreach ($intervals as [$from, $to, $min, $max]) {
                $seconds = $this->dateTime($row[$to])->getTimestamp() - $this->dateTime($row[$from])->getTimestamp();

                $this->assertGreaterThanOrEqual($min, $seconds);
                $this->assertLessThanOrEqual($max, $seconds);
            }
        }
    }

    public function test_it_generates_price_quantity_and_shipping_values(): void
    {
        foreach ($this->rows(100) as $row) {
            $this->assertIsInt($row['판매가']);
            $this->assertGreaterThanOrEqual(1000, $row['판매가']);
            $this->assertLessThanOrEqual(100000, $row['판매가']);
            $this->assertIsInt($row['공급가']);
            $this->assertGreaterThanOrEqual(0, $row['공급가']);
            $this->assertLessThanOrEqual($row['판매가'], $row['공급가']);
            $this->assertIsInt($row['원가']);
            $this->assertGreaterThanOrEqual(0, $row['원가'] - $row['공급가']);
            $this->assertLessThanOrEqual(10000, $row['원가'] - $row['공급가']);
            $this->assertContains($row['주문수량'], [1, 2]);
            $this->assertContains($row['배송방법(원본)'], ['선결제', '무료배송']);
            $this->assertSame($row['배송방법(원본)'], $row['배송방법(묶음/조건적용후)']);
            $this->assertSame($row['배송방법(원본)'] === '무료배송' ? 0 : 3000, $row['배송비금액']);
            $this->assertSame($row['배송비금액'], $row['총배송비금액(묶음후)']);
        }
    }

    /**
     * Generate rows keyed by column name.
     *
     * @return array<int, array<string, mixed>>
     */
    private function rows(int $count): array
    {
        $factory = PlayautoFactory::make()->count($count);

        return array_map(fn (array $row) => array_combine($factory->header(), $row), $factory->create());
    }

    private function dateTime(string $value): DateTimeImmutable
    {
        $value = preg_replace('/(오전|오후) (\d{2}:\d{2}:\d{2})/', '$2 $1', $value);
        $value = str_replace(['오전', '오후'], ['AM', 'PM'], $value);

        $dateTime = DateTimeImmutable::createFromFormat(
            'Y-m-d h:i:s A',
            $value
        );

        $this->assertInstanceOf(DateTimeImmutable::class, $dateTime);

        return $dateTime;
    }
}
