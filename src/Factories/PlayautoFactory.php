<?php

namespace Cable8mm\OrderSheet\Factories;

use Cable8mm\OrderSheet\Enums\OnlineMall;
use Cable8mm\OrderSheet\Support\Faker;

class PlayautoFactory extends Factory
{
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
            '원가' => $faker->numberBetween(0, 100000),
            '공급가' => 0,
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
}
