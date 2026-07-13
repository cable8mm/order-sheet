# 주문서 및 인보이스 생성기

[![code-style](https://github.com/cable8mm/order-sheet/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/order-sheet/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/run-tests.yml)
[![pages-build-deployment](https://github.com/cable8mm/order-sheet/actions/workflows/pages/pages-build-deployment/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/pages/pages-build-deployment)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/order-sheet)](https://packagist.org/packages/cable8mm/order-sheet)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/order-sheet/php?logo=PHP&logoColor=white&color=777BB4)](https://packagist.org/packages/cable8mm/order-sheet)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/order-sheet)](https://packagist.org/packages/cable8mm/order-sheet/stats)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/order-sheet)](https://github.com/cable8mm/order-sheet/stargazers)
[![Packagist License](https://img.shields.io/packagist/l/cable8mm/order-sheet)](https://github.com/cable8mm/order-sheet/blob/main/LICENSE.md)

주문서와 인보이스 생성을 위한 테스트 데이터(시드)를 자동으로 생성하는 PHP 패키지입니다.

API 문서: <https://www.palgle.com/order-sheet/>

## 지원 쇼핑몰

- [x] PLAYAUTO - <https://www.plto.com/>

## 지원 온라인 몰

PLAYAUTO 주문서에서 사용 가능한 온라인 몰:

- 티몬 (Timon)
- 고도몰5 (GodoMall5)
- 카페24(신) (NewCafe24)
- 위메프2.0 (Wemake2)
- 롯데백화점 (LotteDepartment)
- 옥션 (Auction)

## 확장 방법

이 패키지는 새로운 쇼핑몰을 쉽게 추가할 수 있도록 설계되어 있습니다.

### 새 쇼핑몰 추가하기

1. `src/Factories/` 디렉토리에 새 Factory 클래스 생성 (예: `NewCompanyFactory.php`)
2. `definition()` 메서드를 구현하여 컬럼명과 faker 값을 반환하는 배열 작성
3. `src/Enums/OrderSheetType.php` enum에 새 케이스 추가
4. `factoryClass()` 메서드에서 새 케이스를 Factory 클래스에 매핑

예제:

```php
// src/Factories/NewCompanyFactory.php
namespace Cable8mm\OrderSheet\Factories;

class NewCompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            '주문번호' => Faker::shared()->randomNumber(10),
            '주문일' => Faker::make()->dateTime(),
            // ... 더 많은 필드들
        ];
    }
}

// src/Enums/OrderSheetType.php
enum OrderSheetType
{
    case PlayautoType;
    case NewCompanyType; // 새 타입 추가

    public function factoryClass(): string
    {
        return match ($this) {
            self::PlayautoType => PlayautoFactory::class,
            self::NewCompanyType => NewCompanyFactory::class, // 새 Factory 매핑
        };
    }
}
```

## 요구사항

- PHP 8.2 이상

## 설치

```bash
composer require cable8mm/order-sheet
```

## 사용 방법

```php
use Cable8mm\OrderSheet\OrderSheet;
use Cable8mm\OrderSheet\Enums\OrderSheetType;

// 헤더를 포함한 XLSX 파일로 저장
OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)              // 10개의 데이터 생성
  ->header()               // 헤더 행 포함
  ->path('dist')           // 저장할 경로 지정
  ->xlsx('my.xlsx');       // xlsx 파일로 내보내기 (기본: 'order_sheet.xlsx')

// 헤더 없이 CSV 문자열로 내보내기
$csv = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)
  ->csv();

// 헤더 없이 배열로 내보내기
$array = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)
  ->toArray();

// state()로 특정 필드 값 변경
$customData = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(5)
  ->state([
    '상태' => '배송완료',   // 상태 필드 변경
    '구매자명' => '홍길동', // 구매자명 필드 변경
  ])
  ->toArray();
```

### 테스트

```bash
# 테스트 실행
composer test

# 코드 커버리지와 함께 테스트
composer test-coverage

# 코드 스타일 검사
composer lint
```

### 변경사항

[CHANGELOG](CHANGELOG.md)를 참고하세요.

## 기여하기

[CONTRIBUTING](CONTRIBUTING.md)을 참고하세요.

### 보안

보안 관련 문제는 이슈 트래커 대신 <cable8mm@gmail.com>으로 이메일 보내주세요.

## 크레딧

- [Samgu Lee](https://github.com/cable8mm)
- [All Contributors](../../contributors)

## 라이선스

MIT 라이선스. 자세한 내용은 [License File](LICENSE.md)를 참고하세요.
