<?php

namespace Cable8mm\OrderSheet\Factories;

use Cable8mm\OrderSheet\Enums\OnlineMall;
use Cable8mm\OrderSheet\Support\Faker;

class PlayautoFactory extends Factory
{
    private const HEADER = [
        '주문고유번호', '판매사이트명', '판매사이트코드', '판매자ID', '주문수집자ID',
        '수집일', '주문일', '결제일', '상태변경일', '배송일', '상태', '판매사이트 주문번호',
        '판매사이트 상품코드', '상품명', '상품명-홍보문구', '주문선택사항', '주문선택사항금액',
        '추가구매옵션', '추가구매옵션금액', '원가', '공급가', '판매가', '총판매가(묶음후)',
        '주문수량', '총주문수량(묶음후)', '배송방법(원본)', '배송방법(묶음/조건적용후)',
        '배송방법 자동적용 사유', '배송비금액', '총배송비금액(묶음후)', '구매자ID', '구매자명',
        '구매자전화번호', '구매자휴대폰번호', '수령자명', '수령자전화번호', '수령자휴대폰번호',
        '배송지우편번호', '배송지주소', '배송메세지', '배송사명', '송장번호', '마스터상품코드',
        '주의메세지', '판매자상품코드',
    ];

    /**
     * Fields that identify one order rather than one product line.
     */
    private const ORDER_FIELDS = [
        '주문고유번호', '판매사이트명', '판매사이트코드', '판매자ID', '주문수집자ID',
        '수집일', '주문일', '결제일', '상태변경일', '배송일', '상태', '판매사이트 주문번호',
        '배송방법(원본)', '배송방법(묶음/조건적용후)', '배송방법 자동적용 사유', '배송비금액',
        '총배송비금액(묶음후)', '구매자ID', '구매자명', '구매자전화번호', '구매자휴대폰번호',
        '수령자명', '수령자전화번호', '수령자휴대폰번호', '배송지우편번호', '배송지주소',
        '배송메세지', '배송사명', '송장번호',
    ];

    /**
     * Create rows, grouping adjacent rows into one order when requested.
     *
     * @return array<int, array<int, mixed>>
     */
    public function create(): array
    {
        $records = [];

        for ($offset = 0; $offset < $this->count; $offset += $this->bundleSize) {
            $lineCount = min($this->bundleSize, $this->count - $offset);
            $orderLines = [];

            for ($line = 0; $line < $lineCount; $line++) {
                $orderLines[] = $this->applyState($this->definition());
            }

            $orderLines = $this->bundleOrderLines($orderLines);

            foreach ($orderLines as $orderLine) {
                $records[] = array_values($orderLine);
            }
        }

        return $records;
    }

    /**
     * Get the stable column layout of generated PlayAuto samples.
     *
     * @return array<int, string>
     */
    public function header(): array
    {
        return self::HEADER;
    }

    /**
     * Define the order sheet's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::shared();
        $orderAt = $faker->dateTimeBetween('-30 days', '-1 day');
        $collectedAt = (clone $orderAt)->modify('+'.$faker->numberBetween(1, 60).' minutes');
        $paidAt = (clone $collectedAt)->modify('+'.$faker->numberBetween(1, 30).' minutes');
        $statusChangedAt = (clone $paidAt)->modify('+'.$faker->numberBetween(1, 120).' minutes');
        $shippedAt = (clone $statusChangedAt)->modify('+'.$faker->numberBetween(1, 24).' hours');

        $quantity = $faker->numberBetween(1, 2);
        $unitPrice = $faker->numberBetween(1000, 100000);
        $supplyPrice = $faker->numberBetween(0, $unitPrice);
        $costPrice = $supplyPrice + $faker->numberBetween(0, 10000);
        $shippingMethod = $faker->randomElement(['선결제', '무료배송']);
        $shippingFee = $shippingMethod === '무료배송' ? 0 : 3000;
        $dateTime = Faker::make();

        return [
            '주문고유번호' => $faker->randomNumber(3, true).$faker->randomNumber(9, true),
            '판매사이트명' => $faker->randomElement(OnlineMall::values()),
            '판매사이트코드' => $faker->randomElement(['A', 'B']).$faker->randomNumber(3, true),
            '판매자ID' => $faker->word(),
            '주문수집자ID' => $faker->word(),
            '수집일' => $dateTime->dateTime($collectedAt),
            '주문일' => $dateTime->dateTime($orderAt),
            '결제일' => $dateTime->dateTime($paidAt),
            '상태변경일' => $dateTime->dateTime($statusChangedAt),
            '배송일' => $dateTime->dateTime($shippedAt),
            '상태' => '송장입력',
            '판매사이트 주문번호' => '2'.$faker->randomNumber(9, true),
            '판매사이트 상품코드' => $faker->randomNumber(1, true).$faker->randomNumber(9, true),
            '상품명' => $faker->productName(),
            '상품명-홍보문구' => null,
            '주문선택사항' => null,
            '주문선택사항금액' => 0,
            '추가구매옵션' => $faker->randomElement([null, '추가구매상품 없음']),
            '추가구매옵션금액' => 0,
            '원가' => $costPrice,
            '공급가' => $supplyPrice,
            '판매가' => $unitPrice,
            '총판매가(묶음후)' => $unitPrice * $quantity,
            '주문수량' => $quantity,
            '총주문수량(묶음후)' => $quantity,
            '배송방법(원본)' => $shippingMethod,
            '배송방법(묶음/조건적용후)' => $shippingMethod,
            '배송방법 자동적용 사유' => null,
            '배송비금액' => $shippingFee,
            '총배송비금액(묶음후)' => $shippingFee,
            '구매자ID' => $faker->word(),
            '구매자명' => $faker->name(),
            '구매자전화번호' => ($phone = $faker->phoneNumber()),
            '구매자휴대폰번호' => ($cellPhone = $faker->cellPhoneNumber()),
            '수령자명' => $faker->name(),
            '수령자전화번호' => $phone,
            '수령자휴대폰번호' => $cellPhone,
            '배송지우편번호' => $faker->postcode(),
            '배송지주소' => $faker->address(),
            '배송메세지' => $faker->randomElement(['문 앞에 놔 주세요', '', '부재 시 연락 주세요']),
            '배송사명' => $faker->company(),
            '송장번호' => $faker->randomNumber(9) + 1,
            '마스터상품코드' => ($masterCode = $faker->randomNumber(1) + 1),
            '주의메세지' => null,
            '판매자상품코드' => $masterCode,
        ];
    }

    /**
     * Apply common order data and derived bundle totals to every line.
     *
     * @param  array<int, array<string, mixed>>  $orderLines
     * @return array<int, array<string, mixed>>
     */
    private function bundleOrderLines(array $orderLines): array
    {
        $order = $orderLines[0];
        $totalQuantity = 0;
        $totalPrice = 0;

        foreach ($orderLines as $line) {
            $totalQuantity += (int) $line['주문수량'];
            $totalPrice += (int) $line['판매가'] * (int) $line['주문수량'];
        }

        foreach ($orderLines as &$line) {
            foreach (self::ORDER_FIELDS as $field) {
                $line[$field] = $order[$field];
            }

            $line['총판매가(묶음후)'] = $totalPrice;
            $line['총주문수량(묶음후)'] = $totalQuantity;
        }
        unset($line);

        return $orderLines;
    }
}
