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
            '배송지우편번호', '배송지주소', '배송메세지', '배송사명', '송장번호', '마스터상품코드',
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
