# TECH_STACK.md

## 1. Languages / runtimes / frameworks / libraries

- Language: PHP `^8.2`.
- Supported PHP versions: 8.2, 8.3, 8.4, 8.5.
- Package manager: Composer.
- Package name: `cable8mm/order-sheet`.
- Package type: `library`.
- Runtime dependencies:
  - `cable8mm/enum-getter` `^2.0`
  - `fakerphp/faker` `^1.0`
  - `league/csv` `^9.0`
  - `mbezhanov/faker-provider-collection` `^2.0`
  - `phpoffice/phpspreadsheet` `^5.0`
- Development dependencies:
  - `laravel/pint` `^1.20`
  - `phpunit/phpunit` `^11.0`
- API documentation generator: Doctum, the version bundled in `sudo-bot/action-doctum@v5` (verify).
- CI system: GitHub Actions.
- MUST NOT add a dependency without updating this section.
- MUST NOT declare PHP extensions in `composer.json`; extension requirements MUST come from dependencies.

## 2. Framework- or tool-mandated paths and file names

- `composer.json`: package manifest.
- `vendor/`: Composer install directory.
- `vendor/autoload.php`: Composer autoloader.
- `src/`: PSR-4 root for namespace `Cable8mm\OrderSheet\`.
- `tests/`: PSR-4 root for namespace `Cable8mm\OrderSheet\Tests\`.
- `phpunit.xml.dist`: PHPUnit configuration.
- `.github/workflows/`: GitHub Actions workflow files.
- `doctum.php`: Doctum configuration.
- `build/`: Doctum output directory.
- `cache/`: Doctum cache directory.
- `.editorconfig`: editor formatting rules.
- `LICENSE.md`: license text.
- `CHANGELOG.md`: release notes.

## 3. Coding conventions

- Code style MUST conform to Pint with its default `laravel` preset.
- MUST NOT add `pint.json`.
- Files MUST be UTF-8 with LF line endings and a final newline.
- PHP files MUST use 4-space indentation.
- Markdown and YAML files MUST use 2-space indentation.
- Classes MUST follow PSR-4 under `Cable8mm\OrderSheet\`.
- Parameters and return values MUST have native type declarations.
- Backed enums that expose their values MUST use the `Cable8mm\EnumGetter\EnumGetter` trait.

## 4. Testing rules

- Test tool: PHPUnit.
- Test files MUST be in `tests/`.
- Test file names MUST end with `Test.php`.
- Test file paths under `tests/` MUST mirror the namespace path of the tested class under `src/`.
- Test classes MUST be `final` and extend `PHPUnit\Framework\TestCase`.
- Test method names MUST start with `test_` and use snake_case.
- Tests MUST NOT write output during execution.
- Tests MUST be run with `composer test`.
- CI MUST run `composer test` on every PHP version in section 1 on every pull request and every push to `main`.
- Coverage MUST NOT be measured in CI.
- Coverage MUST NOT have a required threshold.
- `composer test-coverage` MUST be run locally only.

## 5. Configuration rules

- None.

## 6. Build, packaging, and release rules

- Lint MUST be run with `composer lint`.
- CI MUST run Pint on every push and commit its fixes.
- CI MUST run `composer validate` before installing dependencies.
- `composer.lock` MUST NOT be committed.
- `.github/`, `dist/`, `tests/`, `.editorconfig`, `.gitattributes`, `.gitignore`, `CHANGELOG.md`, `LICENSE.md`, `phpunit.xml.dist` MUST be marked `export-ignore` in `.gitattributes`.
- Versions MUST follow Semantic Versioning.
- Release tags MUST use the form `vX.Y.Z`.
- Releases MUST be published as GitHub Releases.
- Distribution target: Packagist.
- `CHANGELOG.md` MUST be updated by the `cable8mm/.github` reusable `update-changelog.yml` workflow on release.
- API documentation MUST be built with Doctum from `doctum.php` and deployed to GitHub Pages on every push to `main`.
- License: MIT.

## 7. Security rules

- Secrets MUST NOT be committed.
- `.env` MUST NOT be committed.
- Dependency constraints MUST use caret (`^`) ranges limited to one major version.

## 8. Non-goals

- PHPStan.
- Any static analysis tool other than Pint.
- PCOV.
- CI coverage measurement.
- Committed `composer.lock`.
