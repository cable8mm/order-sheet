# Order Sheets and Invoices Generator

[![code-style](https://github.com/cable8mm/order-sheet/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/code-style.yml)
[![run-tests](https://github.com/cable8mm/order-sheet/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/run-tests.yml)
[![pages-build-deployment](https://github.com/cable8mm/order-sheet/actions/workflows/pages/pages-build-deployment/badge.svg)](https://github.com/cable8mm/order-sheet/actions/workflows/pages/pages-build-deployment)
[![Packagist Version](https://img.shields.io/packagist/v/cable8mm/order-sheet)](https://packagist.org/packages/cable8mm/order-sheet)
[![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/order-sheet/php?logo=PHP&logoColor=white&color=777BB4
)](https://packagist.org/packages/cable8mm/order-sheet)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/order-sheet)](https://packagist.org/packages/cable8mm/order-sheet/stats)
[![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/order-sheet)](https://github.com/cable8mm/order-sheet/stargazers)
[![Packagist License](https://img.shields.io/packagist/l/cable8mm/order-sheet)](https://github.com/cable8mm/order-sheet/blob/main/LICENSE.md)

This package is able to generate a kind of seeds for **order sheets** and **order invoices** so you can use this package for testing as you want to.

We have provided the API Documentation on the web. For more information, please visit <https://www.palgle.com/order-sheet/> ❤️

## Supported Companies

- [x] PLAYAUTO - <https://www.plto.com/>

## Installation

You can install the package via composer:

```bash
composer require cable8mm/order-sheet
```

## Usage

```php
OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)              // 10 of factories
  ->header()               // include the header row
  ->path('dist')           // the saving path
  ->xlsx('my.xlsx');       // export to xlsx file(default: 'order_sheet.xlsx')
// => Save 10 factories with header in 'dist' folder

$orderSheets = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)              // 10 of factories
  ->csv();                 // export to csv
// => Get 10 factories without header row

$orderSheets = OrderSheet::of(OrderSheetType::PlayautoType)
  ->count(10)              // 10 of factories
  ->toArray();             // export to array
// => Get 10 factories array without header row
```

### Testing

```bash
composer test
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
