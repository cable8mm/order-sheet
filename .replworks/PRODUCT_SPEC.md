# PRODUCT_SPEC.md

## Purpose

- The product is a library named `cable8mm/order-sheet`.
- The product generates synthetic order sheet rows for use as test seed data.
- Order sheet: a table in which each row is one order line of an online mall order, laid out in the column layout of an order sheet type.
- Order sheet type: a named column layout and value set. The only order sheet type is `PlayautoType`.
- Order line: one row of an order sheet that represents one product within one order.

## Problem

- Developers building order-processing software need order sheet files and data whose columns match a real order collection service.
- Real order sheets contain personal data and cannot be used as test data.

## Product Goals

- The developer generates a chosen number of order lines of the `PlayautoType` order sheet type.
- The developer receives the order lines as an in-memory table, as CSV text, or as an XLSX file.
- The developer includes or excludes a header row.
- The developer groups consecutive order lines into one order.
- The developer fixes the value of chosen columns in every generated order line.

## Users

- Developer: a person who calls the product from their own code to obtain order sheet test data.

## Inputs

- Order sheet type: one value of `OrderSheetType`. Allowed values: `PlayautoType`.
- Count: integer number of order lines to generate. Default: 1. Minimum: 1.
- Bundle size: integer number of order lines per order. Default: 1. Minimum: 1.
- State: map from column name to value. Default: empty map.
- Header flag: boolean. Default: false.
- Path: directory for the XLSX file, absolute or relative to the current working directory. Default: `dist`.
- Filename: name of the XLSX file. Default: `order_sheet.xlsx`.

## Outputs

- Table: an ordered list of rows. Each row is an ordered list of 45 cell values.
- Header row: the row of the 45 column names of the `PlayautoType` layout, in this order:
  - `주문고유번호`, `판매사이트명`, `판매사이트코드`, `판매자ID`, `주문수집자ID`, `수집일`, `주문일`, `결제일`, `상태변경일`, `배송일`, `상태`, `판매사이트 주문번호`, `판매사이트 상품코드`, `상품명`, `상품명-홍보문구`, `주문선택사항`, `주문선택사항금액`, `추가구매옵션`, `추가구매옵션금액`, `원가`, `공급가`, `판매가`, `총판매가(묶음후)`, `주문수량`, `총주문수량(묶음후)`, `배송방법(원본)`, `배송방법(묶음/조건적용후)`, `배송방법 자동적용 사유`, `배송비금액`, `총배송비금액(묶음후)`, `구매자ID`, `구매자명`, `구매자전화번호`, `구매자휴대폰번호`, `수령자명`, `수령자전화번호`, `수령자휴대폰번호`, `배송지우편번호`, `배송지주소`, `배송메세지`, `배송사명`, `운송장번호`, `마스터상품코드`, `주의메세지`, `판매자상품코드`
- Data row: one order line. Cell N of a data row holds the value of column N of the header row.
- CSV text: the table as comma-separated text, one line per row.
- XLSX file: a workbook with one worksheet that contains the table starting at cell A1.
- Order-level columns: the 29 columns `주문고유번호`, `판매사이트명`, `판매사이트코드`, `판매자ID`, `주문수집자ID`, `수집일`, `주문일`, `결제일`, `상태변경일`, `배송일`, `상태`, `판매사이트 주문번호`, `배송방법(원본)`, `배송방법(묶음/조건적용후)`, `배송방법 자동적용 사유`, `배송비금액`, `총배송비금액(묶음후)`, `구매자ID`, `구매자명`, `구매자전화번호`, `구매자휴대폰번호`, `수령자명`, `수령자전화번호`, `수령자휴대폰번호`, `배송지우편번호`, `배송지주소`, `배송메세지`, `배송사명`, `운송장번호`.
- Date-time format: `YYYY-MM-DD <meridiem> hh:mm:ss`, 12-hour clock, where `<meridiem>` is `오전` for AM and `오후` for PM.
- Generated names, phone numbers, postcodes, addresses, and company names are Korean-locale values.

## Constraints

- None stated.

## External Systems

- PlayAuto (https://www.plto.com/) order sheet format.
  - Behavior relied on: the `PlayautoType` column layout mirrors the PlayAuto order sheet column layout.

### PlayAuto column layout

- Observation sources: a system that consumed real PlayAuto order sheet files by column position, and human confirmation.
- The PlayAuto order sheet has 45 columns.
- The PlayAuto order sheet columns are, in order, the 45 column names listed in Outputs.
- The PlayAuto order sheet has one header row followed by data rows.
- Consecutive data rows with the same `주문고유번호` belong to one order.
- `수집일`, `주문일`, `결제일`, `상태변경일`, `배송일` use the date-time format.

### PlayAuto value formats [UNVERIFIED]

- The value format of every column other than `수집일`, `주문일`, `결제일`, `상태변경일`, `배송일` has not been observed.

## User Interface

- The product has no graphical interface.
- The interface is a set of chainable operations on an order sheet builder.
- Order sheet builder: the object returned by EL-001 that holds the inputs for one order sheet type.

### Operations

- EL-001 `OrderSheet::of(OrderSheetType $type)`
  - Purpose: create an order sheet builder for the order sheet type.
  - Result: an order sheet builder with every input at its default.
- EL-002 `count(int $count)`
  - Purpose: set the count.
  - Result: the same order sheet builder.
- EL-003 `bundle(int $bundleSize)`
  - Purpose: set the bundle size.
  - Result: the same order sheet builder.
- EL-004 `state(array $state)`
  - Purpose: set the state.
  - Result: the same order sheet builder.
- EL-005 `header(bool $header = true)`
  - Purpose: set the header flag.
  - Result: the same order sheet builder.
- EL-006 `path(string $path)`
  - Purpose: set the path.
  - Result: the same order sheet builder.
- EL-007 `toArray()`
  - Purpose: generate the table.
  - Result: the table.
- EL-008 `csv()`
  - Purpose: generate the table as CSV text.
  - Result: CSV text.
- EL-009 `xlsx(string $filename = 'order_sheet.xlsx')`
  - Purpose: generate the table as an XLSX file.
  - Result: an XLSX file at `<path>/<filename>`. No return value.
- EL-010 string conversion of an order sheet builder
  - Purpose: identify the order sheet type.
  - Result: the name of the order sheet type.

### Interactions

- EL-002 with count >= 1 -> later EL-007, EL-008, EL-009 calls produce count data rows.
- EL-002 with count < 1 -> ERR-001.
- EL-003 with bundle size >= 1 -> later EL-007, EL-008, EL-009 calls group data rows per FR-020.
- EL-003 with bundle size < 1 -> ERR-002.
- EL-004 -> the given state replaces any state set before on the same order sheet builder.
- EL-005 with true -> later EL-007, EL-008, EL-009 calls place the header row as the first row.
- EL-005 with false -> later EL-007, EL-008, EL-009 calls produce no header row.
- EL-007, EL-008, EL-009 -> each call generates new random data rows.

## Functional Requirements

### Builder

- FR-001: EL-001 MUST accept `OrderSheetType::PlayautoType`.
- FR-002: EL-002, EL-003, EL-004, EL-005, EL-006 MUST return the order sheet builder they are called on.
- FR-003: EL-010 MUST return `PlayautoType` for an order sheet builder of `PlayautoType`.
- FR-004: EL-002 MUST reject a count below 1 at the time of the EL-002 call with ERR-001.
- FR-005: EL-003 MUST reject a bundle size below 1 at the time of the EL-003 call with ERR-002.

### Table

- FR-006: EL-007 MUST return exactly count data rows.
- FR-007: When the header flag is true, EL-007 MUST return the header row followed by count data rows.
- FR-008: When the header flag is false, EL-007 MUST return no header row.
- FR-009: Every row returned by EL-007 MUST contain exactly 45 cells.
- FR-010: The header row MUST equal the 45 column names listed in Outputs, in the listed order.

### CSV

- FR-011: EL-008 MUST return the table of FR-006 to FR-010 as CSV text.
- FR-012: The CSV text MUST contain one line per row of the table.

### XLSX

- FR-013: EL-009 MUST write the table of FR-006 to FR-010 to the file `<path>/<filename>`.
- FR-014: EL-009 without a filename MUST write the file `<path>/order_sheet.xlsx`.
- FR-015: EL-009 without a prior EL-006 call MUST write into the directory `dist` relative to the current working directory.
- FR-016: EL-009 MUST fail with ERR-003 when the path does not exist.
- FR-017: EL-009 MUST fail with ERR-004 when the path is not writable.

### State

- FR-018: For each state key that is a column name, every data row MUST hold the state value in that column.
- FR-019: For each state key that is not a column name, the product MUST raise ERR-005 and MUST generate the table without that key.

### Bundling

- FR-020: Data rows MUST be grouped into orders of bundle size consecutive data rows, starting from the first data row.
- FR-021: When count is not a multiple of bundle size, the last order MUST contain the remaining count modulo bundle size data rows.
- FR-022: Every data row of one order MUST hold the values of the first data row of that order in every order-level column.
- FR-023: `총주문수량(묶음후)` of every data row of one order MUST equal the sum of `주문수량` over the data rows of that order.
- FR-024: `총판매가(묶음후)` of every data row of one order MUST equal the sum of `판매가` multiplied by `주문수량` over the data rows of that order.
- FR-025: FR-023 and FR-024 MUST be computed after state is applied.
- FR-026: The values of FR-023 and FR-024 MUST replace any state value given for `총주문수량(묶음후)` and `총판매가(묶음후)`.
- FR-027: With bundle size 1, every data row MUST be its own order.

### Generated values

- FR-028: `주문고유번호` MUST be a 12-digit numeric string whose first digit is not 0.
- FR-029: `판매사이트명` MUST be one of `티몬`, `고도몰5`, `카페24(신)`, `위메프2.0`, `롯데백화점`, `옥션`.
- FR-030: `판매사이트코드` MUST be `A` or `B` followed by 3 digits whose first digit is not 0.
- FR-031: `판매자ID`, `주문수집자ID`, `구매자ID` MUST each be a single generated word.
- FR-032: `주문일` MUST be between 30 days before generation and 1 day before generation.
- FR-033: `수집일` MUST be 1 to 60 minutes after `주문일`.
- FR-034: `결제일` MUST be 1 to 30 minutes after `수집일`.
- FR-035: `상태변경일` MUST be 1 to 120 minutes after `결제일`.
- FR-036: `배송일` MUST be 1 to 24 hours after `상태변경일`.
- FR-037: `수집일`, `주문일`, `결제일`, `상태변경일`, `배송일` MUST use the date-time format.
- FR-038: `상태` MUST be `송장입력`.
- FR-039: `판매사이트 주문번호` MUST be `2` followed by 9 digits.
- FR-040: `판매사이트 상품코드` MUST be a 10-digit numeric string whose first digit is not 0.
- FR-041: `상품명` MUST be a generated product name.
- FR-042: `상품명-홍보문구`, `주문선택사항`, `배송방법 자동적용 사유`, `주의메세지` MUST be empty.
- FR-043: `주문선택사항금액` and `추가구매옵션금액` MUST be 0.
- FR-044: `추가구매옵션` MUST be empty or one fixed non-empty value.
- FR-045: `판매가` MUST be an integer from 1000 to 100000.
- FR-046: `공급가` MUST be an integer from 0 to `판매가`.
- FR-047: `원가` MUST equal `공급가` plus an integer from 0 to 10000.
- FR-048: `주문수량` MUST be 1 or 2.
- FR-049: `배송방법(원본)` MUST be `선결제` or `무료배송`.
- FR-050: `배송방법(묶음/조건적용후)` MUST equal `배송방법(원본)`.
- FR-051: `배송비금액` MUST be 0 when `배송방법(원본)` is `무료배송`.
- FR-052: `배송비금액` MUST be 3000 when `배송방법(원본)` is `선결제`.
- FR-053: `총배송비금액(묶음후)` MUST equal `배송비금액`.
- FR-054: `구매자명` and `수령자명` MUST each be an independently generated Korean personal name.
- FR-055: `수령자전화번호` MUST equal `구매자전화번호`.
- FR-056: `수령자휴대폰번호` MUST equal `구매자휴대폰번호`.
- FR-057: `배송지우편번호` MUST be a generated postcode.
- FR-058: `배송지주소` MUST be a generated address.
- FR-059: `배송메세지` MUST be one of three fixed values, one of which is empty.
- FR-060: `배송사명` MUST be a generated company name.
- FR-061: `운송장번호` MUST be an integer from 1 to 1000000000.
- FR-062: `마스터상품코드` MUST be an integer from 1 to 10.
- FR-063: `판매자상품코드` MUST equal `마스터상품코드`.
- FR-064: FR-028 to FR-063 MUST hold for every column not overridden by state.

## User Flows

- UF-001 XLSX file
  - EL-001 with `PlayautoType` -> order sheet builder.
  - EL-002, EL-005, EL-006 -> inputs set.
  - EL-009 -> XLSX file written per FR-013.
- UF-002 CSV text
  - EL-001 with `PlayautoType` -> order sheet builder.
  - EL-002 -> count set.
  - EL-008 -> CSV text per FR-011.
- UF-003 Table
  - EL-001 with `PlayautoType` -> order sheet builder.
  - EL-002 -> count set.
  - EL-007 -> table per FR-006.
- UF-004 Bundled orders
  - EL-001 with `PlayautoType` -> order sheet builder.
  - EL-002, EL-003 -> count and bundle size set.
  - EL-007 -> table grouped per FR-020 to FR-024.
- UF-005 Fixed column values
  - EL-001 with `PlayautoType` -> order sheet builder.
  - EL-002, EL-004 -> count and state set.
  - EL-007 -> table with state values per FR-018.

## Error Conditions

- ERR-001: count below 1 passed to EL-002.
  - The call MUST throw `InvalidArgumentException` with message `Count must be at least 1`.
- ERR-002: bundle size below 1 passed to EL-003.
  - The call MUST throw `InvalidArgumentException` with message `Bundle size must be at least 1`.
- ERR-003: path does not exist during EL-009.
  - The call MUST throw `RuntimeException` with message `Directory "<path>" does not exist`.
  - No file MUST be written.
- ERR-004: path is not writable during EL-009.
  - The call MUST throw `RuntimeException` with message `Directory "<path>" is not writable`.
  - No file MUST be written.
- ERR-005: state key that is not a column name during EL-007, EL-008, or EL-009.
  - The product MUST raise a user-level warning with message `State key "<key>" does not exist in definition and will be ignored`.
  - Generation MUST continue without that key.

## Non-Goals

- The product MUST NOT provide data verified against real PlayAuto files.

## Acceptance Criteria

- AC-001: EL-010 on EL-001 with `PlayautoType` returns `PlayautoType` (FR-003).
- AC-002: EL-002 with 0 throws ERR-001 (FR-004).
- AC-003: EL-003 with 0 throws ERR-002 (FR-005).
- AC-004: EL-007 with count 2 and header flag false returns 2 rows (FR-006, FR-008).
- AC-005: EL-007 with count 3 and header flag true returns 4 rows whose first row equals the header row (FR-007, FR-010).
- AC-006: Every row returned by EL-007 contains 45 cells (FR-009).
- AC-007: EL-008 with count 5 and header flag false returns 5 lines (FR-011, FR-012).
- AC-008: EL-008 with count 3 and header flag true returns 4 lines whose first line contains `주문고유번호` (FR-011, FR-012).
- AC-009: EL-009 without a filename after EL-006 with `dist` creates `dist/order_sheet.xlsx` (FR-013, FR-014).
- AC-010: EL-009 with `make_custom_name.xlsx` after EL-006 with `dist` creates `dist/make_custom_name.xlsx` (FR-013).
- AC-011: EL-009 after EL-006 with `/nonexistent/path` throws ERR-003 (FR-016).
- AC-012: EL-009 after EL-006 with a non-writable directory throws ERR-004 (FR-017).
- AC-013: EL-007 after EL-004 with `상태` = `배송완료`, `구매자명` = `홍길동`, `주문수량` = 5 returns data rows holding those values in those columns (FR-018).
- AC-014: EL-007 after EL-004 with a key that is not a column name raises ERR-005 (FR-019).
- AC-015: EL-007 with count 3 and bundle size 2 returns 3 data rows, grouped as 2 and 1 (FR-020, FR-021).
- AC-016: EL-007 with count 4 and bundle size 2 returns data rows in which each order of 2 rows holds equal values in every order-level column (FR-022).
- AC-017: In AC-016, `총주문수량(묶음후)` and `총판매가(묶음후)` of every data row equal the sums of FR-023 and FR-024.
- AC-018: EL-007 with bundle size 1 returns data rows in which `총주문수량(묶음후)` equals `주문수량` and `총판매가(묶음후)` equals `판매가` multiplied by `주문수량` (FR-027).
- AC-019: EL-007 after EL-004 with `주문수량` = 5 and bundle size 1 returns `총주문수량(묶음후)` = 5 (FR-025).
- AC-020: EL-007 after EL-004 with `총주문수량(묶음후)` = 99 returns data rows in which `총주문수량(묶음후)` equals the sum of FR-023 (FR-026).
- AC-021: In every data row, `주문일` <= `수집일` <= `결제일` <= `상태변경일` <= `배송일` (FR-032 to FR-036).
- AC-022: In every data row, date-time columns match the date-time format (FR-037).
- AC-023: In every data row, `원가` >= `공급가` and `공급가` <= `판매가` (FR-046, FR-047).
- AC-024: In every data row, `배송비금액` is 0 when `배송방법(원본)` is `무료배송` and 3000 otherwise (FR-051, FR-052).
- AC-025: In every data row, `판매사이트명` is one of the six values of FR-029.
- AC-026: In every data row, `수령자전화번호` equals `구매자전화번호` and `수령자휴대폰번호` equals `구매자휴대폰번호` (FR-055, FR-056).
- AC-027: In every data row, `판매자상품코드` equals `마스터상품코드` (FR-063).
