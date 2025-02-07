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
        return [
            '주문고유번호' => Faker::shared()->randomNumber(3, true).Faker::shared()->randomNumber(9, true),
            '판매사이트명' => Faker::shared()->randomElement(OnlineMall::values()),
            '판매사이트코드' => Faker::shared()->randomElement(['A', 'B']).Faker::shared()->randomNumber(3, true),
            '판매자ID' => Faker::shared()->word(),
            '주문수집자ID' => Faker::shared()->word(),
            '수집일' => Faker::make()->dateTime(),
            '주문일' => Faker::make()->dateTime(),
            '결제일' => Faker::make()->dateTime(),
            '상태변경일' => Faker::make()->dateTime(),
            '배송일' => Faker::make()->dateTime(),
            '상태' => '송장입력',
            '판매사이트 주문번호' => '2'.Faker::shared()->randomNumber(9, true),
            '판매사이트 상품코드' => Faker::shared()->randomNumber(1, true).Faker::shared()->randomNumber(9, true),
            '상품명' => Faker::shared()->productName(),
            '상품명-홍보문구' => null,
            '주문선택사항' => null,
            '주문선택사항금액' => 0,
            '추가구매옵션' => Faker::shared()->randomElement([null, '추가구매상품 없음']),
            '추가구매옵션금액' => 0,
            '원가' => ($costPrice = Faker::shared()->numberBetween(0, 100000)),
            '공급가' => 0,
            '판매가' => ($salePrice = $costPrice * Faker::shared()->randomFloat(1, 0, 1)),
            '총판매가(묶음후)' => $salePrice,
            '주문수량' => Faker::shared()->numberBetween(1, 2),
            '총주문수량(묶음후)' => Faker::shared()->numberBetween(2, 10),
            '배송방법(원본)' => Faker::shared()->randomElement(['선결제', '무료배송']),
            '배송방법(묶음/조건적용후)' => Faker::shared()->randomElement(['선결제', '무료배송']),
            '배송방법 자동적용 사유' => Faker::shared()->randomElement(['묶인 주문 내에 선결제건이 포함됨', '묶인 주문 내에 무료배송건이 포함됨']),
            '배송비금액' => Faker::shared()->randomElement([0, 3000]),
            '총배송비금액(묶음후)' => Faker::shared()->randomElement([0, 3000]),
            '구매자ID' => Faker::shared()->word(),
            '구매자명' => Faker::shared()->name(),
            '구매자전화번호' => ($phone = Faker::shared()->phoneNumber()),
            '구매자휴대폰번호' => ($cellPhone = Faker::shared()->cellPhoneNumber()),
            '수령자명' => Faker::shared()->name(),
            '수령자전화번호' => $phone,
            '수령자휴대폰번호' => $cellPhone,
            '배송지우편번호' => Faker::shared()->postcode(),
            '배송지주소' => Faker::shared()->address(),
            '배송메세지' => Faker::shared()->randomElement(['문 앞에 놔 주세요', '', '부재 시 연락 주세요']),
            '배송사명' => Faker::shared()->company(),
            '송장번호' => Faker::shared()->randomNumber(9) + 1,
            '마스터상품코드' => ($masterCode = Faker::shared()->randomNumber(1) + 1),
            '주의메세지' => null,
            '판매자상품코드' => $masterCode,
        ];
    }
}
