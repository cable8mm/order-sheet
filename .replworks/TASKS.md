# TASKS.md

- [x] T-001 Project foundation
  - Satisfies: TECH_STACK.md (all sections)
  - External boundary: no
  - Acceptance criteria:
    - Every constraint in TECH_STACK.md is satisfied by the repository.
    - Every check defined in TECH_STACK.md passes locally.
    - Every CI check defined in TECH_STACK.md passes on `main`.

- [x] T-002 Order sheet builder creation and string conversion
  - Satisfies: PRODUCT_SPEC.md User Interface (EL-001, EL-010), FR-001, FR-002, FR-003
  - External boundary: no
  - Acceptance criteria:
    - EL-001 with `PlayautoType` returns an order sheet builder.
    - EL-002, EL-003, EL-004, EL-005, EL-006 each return the same order sheet builder instance they are called on.
    - EL-010 returns `PlayautoType` (AC-001).
    - Unit tests for every criterion above pass.

- [ ] T-003 Table generation by count
  - Satisfies: PRODUCT_SPEC.md FR-004, FR-006, FR-008, FR-009, ERR-001, User Interface (Interactions)
  - External boundary: no
  - Acceptance criteria:
    - EL-007 without EL-002 returns 1 data row.
    - EL-007 with count N returns exactly N data rows for N in 1, 2, 10 (AC-004).
    - Every row returned by EL-007 contains 45 cells (AC-006).
    - EL-002 with 0 and with -1 throws ERR-001 at the EL-002 call (AC-002).
    - Two EL-007 calls on the same order sheet builder return different data rows.
    - Unit tests for every criterion above pass.

- [ ] T-004 Header row
  - Satisfies: PRODUCT_SPEC.md FR-007, FR-008, FR-010, Outputs (header row), User Interface (EL-005)
  - External boundary: no
  - Acceptance criteria:
    - EL-007 with header flag true and count 3 returns 4 rows (AC-005).
    - The first row equals the 45 column names of PRODUCT_SPEC.md Outputs, in order (AC-005).
    - EL-005 with true followed by EL-005 with false makes EL-007 return no header row.
    - The header row has the same length as every data row.
    - Unit tests for every criterion above pass.

- [ ] T-005 Rename column 41 header to 운송장번호
  - Satisfies: PRODUCT_SPEC.md Outputs (header row, order-level columns), FR-010, FR-061, External Systems / PlayAuto column layout
  - External boundary: no
  - Acceptance criteria:
    - The `PlayautoType` header row and data rows use `운송장번호` in place of `송장번호` at column 41.
    - The header row returned by `toArray()` with the header flag true has `운송장번호` at column 41.
    - No row returned by `toArray()` contains the header value `송장번호`.
    - Bundled orders keep `운송장번호` identical across all data rows of one order (FR-022).
    - The `PlayautoType` column layout test lists `운송장번호` at column 41.
    - All tests pass.

- [ ] T-006 Order, site, and product identifier values
  - Satisfies: PRODUCT_SPEC.md FR-028, FR-029, FR-030, FR-031, FR-038, FR-039, FR-040, FR-041, FR-042, FR-043, FR-044, FR-060, FR-061, FR-062, FR-063
  - External boundary: no
  - Acceptance criteria:
    - Over 100 generated data rows, every column named in FR-028 to FR-031, FR-038 to FR-044, and FR-060 to FR-063 satisfies its requirement.
    - `판매사이트명` is one of the six values of FR-029 in every data row (AC-025).
    - `판매자상품코드` equals `마스터상품코드` in every data row (AC-027).
    - Unit tests for every criterion above pass.

- [ ] T-007 Date-time values
  - Satisfies: PRODUCT_SPEC.md FR-032, FR-033, FR-034, FR-035, FR-036, FR-037, Outputs (date-time format)
  - External boundary: no
  - Acceptance criteria:
    - Over 100 generated data rows, `주문일` is between 30 days and 1 day before generation.
    - Over 100 generated data rows, each interval of FR-033 to FR-036 is within its stated range.
    - `주문일` <= `수집일` <= `결제일` <= `상태변경일` <= `배송일` in every data row (AC-021).
    - Every date-time column matches the date-time format, with `오전` for AM and `오후` for PM (AC-022).
    - Unit tests for every criterion above pass.

- [ ] T-008 Price, quantity, and shipping values
  - Satisfies: PRODUCT_SPEC.md FR-045, FR-046, FR-047, FR-048, FR-049, FR-050, FR-051, FR-052, FR-053
  - External boundary: no
  - Acceptance criteria:
    - Over 100 generated data rows, every column named in FR-045 to FR-053 satisfies its requirement.
    - `원가` >= `공급가` and `공급가` <= `판매가` in every data row (AC-023).
    - `배송비금액` is 0 for `무료배송` and 3000 for `선결제` in every data row (AC-024).
    - Unit tests for every criterion above pass.

- [ ] T-009 Buyer, recipient, and delivery values
  - Satisfies: PRODUCT_SPEC.md FR-054, FR-055, FR-056, FR-057, FR-058, FR-059, Outputs (Korean-locale values)
  - External boundary: no
  - Acceptance criteria:
    - Over 100 generated data rows, every column named in FR-054 to FR-059 satisfies its requirement.
    - `구매자명` and `수령자명` contain Hangul characters in every data row.
    - `수령자전화번호` equals `구매자전화번호` and `수령자휴대폰번호` equals `구매자휴대폰번호` in every data row (AC-026).
    - `배송메세지` takes at most three distinct values, one of which is empty.
    - Unit tests for every criterion above pass.

- [ ] T-010 State overrides
  - Satisfies: PRODUCT_SPEC.md FR-018, FR-019, FR-064, ERR-005, User Interface (EL-004)
  - External boundary: no
  - Acceptance criteria:
    - EL-007 after EL-004 with `상태` = `배송완료`, `구매자명` = `홍길동`, `주문수량` = 5 returns those values in those columns of every data row (AC-013).
    - A second EL-004 call replaces the state of the first EL-004 call.
    - EL-007 after EL-004 with a key that is not a column name raises ERR-005 with the key in the message (AC-014).
    - With ERR-005 raised, generation continues and returns count data rows without the unknown key.
    - Columns not named in state still satisfy FR-028 to FR-063.
    - Unit tests for every criterion above pass.

- [ ] T-011 Bundled orders
  - Satisfies: PRODUCT_SPEC.md FR-005, FR-020, FR-021, FR-022, FR-023, FR-024, FR-025, FR-026, FR-027, ERR-002, User Interface (EL-003)
  - External boundary: no
  - Acceptance criteria:
    - EL-003 with 0 and with -1 throws ERR-002 at the EL-003 call (AC-003).
    - EL-007 with count 3 and bundle size 2 returns orders of 2 and 1 data rows (AC-015).
    - Every order holds equal values in every order-level column (AC-016).
    - `총주문수량(묶음후)` and `총판매가(묶음후)` equal the sums of FR-023 and FR-024 in every data row (AC-017).
    - With bundle size 1, totals equal the row's own values (AC-018).
    - EL-004 with `주문수량` = 5 yields `총주문수량(묶음후)` = 5 with bundle size 1 (AC-019).
    - EL-004 with `총주문수량(묶음후)` = 99 yields the computed sum, not 99 (AC-020).
    - Unit tests for every criterion above pass.

- [ ] T-012 CSV output
  - Satisfies: PRODUCT_SPEC.md FR-011, FR-012, Outputs (CSV text), User Interface (EL-008)
  - External boundary: no
  - Acceptance criteria:
    - EL-008 with count 5 and header flag false returns 5 lines (AC-007).
    - EL-008 with count 3 and header flag true returns 4 lines whose first line contains `주문고유번호` (AC-008).
    - Every CSV line parses to 45 fields.
    - EL-008 after EL-004 returns the state values in the parsed lines.
    - Unit tests for every criterion above pass.

- [ ] T-013 XLSX output
  - Satisfies: PRODUCT_SPEC.md FR-013, FR-014, FR-015, FR-016, FR-017, ERR-003, ERR-004, Outputs (XLSX file), User Interface (EL-006, EL-009)
  - External boundary: yes (local file system)
  - Acceptance criteria:
    - EL-009 without a filename after EL-006 with `dist` creates `dist/order_sheet.xlsx` on the live file system (AC-009).
    - EL-009 with `make_custom_name.xlsx` creates `dist/make_custom_name.xlsx` on the live file system (AC-010).
    - EL-009 without EL-006 writes into `dist` relative to the current working directory.
    - An E2E test reads the written file back and finds one worksheet whose rows from cell A1 equal the table, including the header row when the header flag is true.
    - EL-009 with a nonexistent path throws ERR-003 and writes no file (AC-011).
    - EL-009 with a non-writable directory on the live file system throws ERR-004 and writes no file (AC-012).
    - Unit tests and the E2E test for every criterion above pass.
