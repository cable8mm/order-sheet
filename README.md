# Order Sheets and Invoices Generator

[![code-style](https://github.com/cable8mm/order-sheet/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/order-sheet/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/run-tests.yml)
[![pages-build-deployment](https://github.com/cable8mm/order-sheet/actions/workflows/pages/pages-build-deployment/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/pages/pages-build-deployment)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/order-sheet)](https://packagist.org/packages/cable8mm/order-sheet)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/order-sheet/php?logo=PHP&logoColor=white&color=777BB4)](https://packagist.org/packages/cable8mm/order-sheet)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/order-sheet)](https://packagist.org/packages/cable8mm/order-sheet/stats)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/order-sheet)](https://github.com/cable8mm/order-sheet/stargazers)
[![Packagist License](https://img.shields.io/packagist/l/cable8mm/order-sheet)](https://github.com/cable8mm/order-sheet/blob/main/LICENSE.md)

This package is able to generate a kind of seeds for **order sheets** and **order invoices** so you can use this package for testing as you want to.

We have provided the API Documentation on the web. For more information, please visit <https://www.palgle.com/order-sheet/> ❤️

## Supported Companies

- [x] PLAYAUTO - <https://www.plto.com/>

## Extending

This package is designed to be extensible. To add support for a new company:

1. Create a new Factory class in `src/Factories/` directory (e.g., `NewCompanyFactory.php`)
2. Implement the `definition()` method that returns an array of column names and their faker values
3. Add a new case to the `OrderSheetType` enum in `src/Enums/OrderSheetType.php`
4. Map the new case to your factory class in the `factoryClass()` method

Example:

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
            // ... more fields
        ];
    }
}

// src/Enums/OrderSheetType.php
enum OrderSheetType
{
    case PlayautoType;
    case NewCompanyType; // Add new type

    public function factoryClass(): string
    {
        return match ($this) {
            self::PlayautoType => PlayautoFactory::class,
            self::NewCompanyType => NewCompanyFactory::class, // Map to new factory
        };
    }
}
```

## Requirements

- PHP 8.2 or higher

## Installation

You can install the package via composer:

```bash
composer require cable8mm/order-sheet
```

## Usage

```php
use Cable8mm\OrderSheet\OrderSheet;
use Cable8mm\OrderSheet\Enums\OrderSheetType;

// Export to XLSX file with header
OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)              // Create 10 rows
  ->header()               // Include the header row
  ->path('dist')           // Set the saving path
  ->xlsx('my.xlsx');       // Export to xlsx file (default: 'order_sheet.xlsx')
// => Saves 10 rows with header in 'dist/my.xlsx'

// Export to CSV string without header
$orderSheets = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)              // Create 10 rows
  ->csv();                 // Export to CSV string
// => Returns CSV string with 10 rows (no header)

// Export to array without header
$orderSheets = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)              // Create 10 rows
  ->toArray();             // Export to array
// => Returns array with 10 rows (no header)

// Customize data with state
$orderSheets = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(5)               // Create 5 rows
  ->state([
    '상태' => '배송완료',   // Override the '상태' field
    '구매자명' => '홍길동', // Override the '구매자명' field
  ])
  ->toArray();
// => Returns array with 5 rows where '상태' is '배송완료' and '구매자명' is '홍길동'
```

### Testing

Run the test suite:

```bash
composer test
```

Run the test suite with coverage:

```bash
composer test-coverage
```

Check code style:

```bash
composer lint
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email <cable8mm@gmail.com> instead of using the issue tracker.

## Credits

- [Samgu Lee](https://github.com/cable8mm)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
