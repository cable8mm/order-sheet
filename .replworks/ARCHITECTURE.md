# ARCHITECTURE.md

## Purpose

- Define how an order sheet builder turns inputs into a table and serializes that table.
- Define which component owns input validation, row generation, state application, bundling, header production, and serialization.
- Define the invariants that every order sheet type and every output format MUST preserve.

## Core Concepts

- Order sheet builder: the single entry point that holds the inputs for one order sheet type and triggers generation.
- Order sheet type: a key that selects exactly one row factory.
- Row factory: the component that produces data rows and the header row for one order sheet type.
- Row definition: one generated mapping from column name to value, in column order, before state is applied.
- State: a mapping from column name to value that replaces generated values.
- Order: a group of consecutive data rows that share order-level column values.
- Order-level columns: the columns whose values identify an order rather than an order line. The set is owned by each row factory.
- Bundle totals: columns whose values are derived from all data rows of one order.
- Table: the header row (optional) followed by data rows. The table is the only intermediate form between generation and serialization.
- Value generator: the process-wide source of random values.

## System Flow

1. The caller creates an order sheet builder for an order sheet type.
2. The order sheet builder resolves the order sheet type to its row factory.
3. The caller sets count, bundle size, state, header flag, and path on the order sheet builder.
4. The order sheet builder validates count and bundle size at the moment each is set.
5. The caller requests an output: table, CSV text, or XLSX file.
6. For an XLSX file, the order sheet builder checks the path before any generation.
7. The order sheet builder creates a new row factory instance and passes it state, count, and bundle size.
8. The row factory produces data rows:
   1. Split count into orders of bundle size; the last order holds the remainder.
   2. For each order line, produce a row definition from the value generator.
   3. Apply state to the row definition.
   4. Copy order-level column values from the first row of the order to every row of the order.
   5. Compute bundle totals over the rows of the order and write them to every row of the order.
   6. Drop column names; keep values in column order.
9. If the header flag is true, the order sheet builder prepends the header row obtained from the row factory.
10. The order sheet builder returns the table, or serializes the table to CSV text, or writes the table to the XLSX file.

## Components

- C-01 Order Sheet Builder
- C-02 Order Sheet Type Registry
- C-03 Row Factory Contract
- C-04 PlayAuto Row Factory
- C-05 Value Generator
- C-06 Date-Time Formatter
- C-07 Online Mall Catalog

## Component Responsibilities

### C-01 Order Sheet Builder

- Responsibilities:
  - Hold count, bundle size, state, header flag, and path for one order sheet type.
  - Validate count and bundle size when they are set.
  - Check that the path exists and is writable before XLSX generation.
  - Request data rows and the header row from the row factory.
  - Assemble the table.
  - Serialize the table to CSV text.
  - Write the table to an XLSX file at `<path>/<filename>`.
  - Return the order sheet type name for string conversion.
- Inputs: order sheet type, count, bundle size, state, header flag, path, filename.
- Outputs: table, CSV text, XLSX file, order sheet type name, ERR-001, ERR-002, ERR-003, ERR-004.
- Ownership boundary:
  - Owns all caller-facing input validation.
  - Owns all serialization and file writing.
  - MUST NOT generate, modify, or reorder cell values.
  - MUST NOT know column names.

### C-02 Order Sheet Type Registry

- Responsibilities:
  - Map each order sheet type to exactly one row factory.
- Inputs: order sheet type.
- Outputs: the row factory for that type.
- Ownership boundary:
  - Owns the list of supported order sheet types.
  - MUST NOT hold inputs or produce rows.

### C-03 Row Factory Contract

- Responsibilities:
  - Define the operations every row factory provides: accept state, count, and bundle size; produce data rows; produce the header row; produce one row definition.
  - Apply state to a row definition.
  - Raise ERR-005 for each state key that is not a column name, once per row definition it is applied to.
  - Provide default data-row production without bundling: count independent rows.
  - Provide a default header row equal to the column names of one row definition, in order.
- Inputs: state, count, bundle size, row definition.
- Outputs: data rows, header row, ERR-005.
- Ownership boundary:
  - Owns state application.
  - MUST NOT serialize or write files.
  - MUST NOT define column names or values.

### C-04 PlayAuto Row Factory

- Responsibilities:
  - Define the `PlayautoType` row definition: 45 columns in the order of PRODUCT_SPEC.md Outputs.
  - Define the fixed `PlayautoType` header row.
  - Define the `PlayautoType` order-level columns.
  - Generate every column value per PRODUCT_SPEC.md FR-028 to FR-063.
  - Group data rows into orders per bundle size.
  - Copy order-level column values from the first row of each order.
  - Compute `총주문수량(묶음후)` and `총판매가(묶음후)` per order.
- Inputs: state, count, bundle size, values from C-05, formatted date-times from C-06, mall names from C-07.
- Outputs: data rows, header row.
- Ownership boundary:
  - Owns the `PlayautoType` column layout, value rules, order-level column set, and bundle totals.
  - MUST NOT validate caller inputs.
  - MUST NOT know the output format.

### C-05 Value Generator

- Responsibilities:
  - Provide random values: numbers, words, names, phone numbers, postcodes, addresses, company names, product names, dates.
  - Produce values for the Korean locale.
  - Keep one shared instance for the whole process.
- Inputs: value requests from C-04 and C-06.
- Outputs: random values.
- Ownership boundary:
  - Owns randomness.
  - MUST NOT know column names or order sheet types.

### C-06 Date-Time Formatter

- Responsibilities:
  - Format a date-time value in the date-time format of PRODUCT_SPEC.md Outputs.
  - Produce a random date-time from C-05 when no value is given.
- Inputs: date-time value or none.
- Outputs: formatted date-time text.
- Ownership boundary:
  - Owns the date-time format.
  - MUST NOT decide which date-time to format.

### C-07 Online Mall Catalog

- Responsibilities:
  - Hold the fixed list of online mall names allowed in `판매사이트명`.
- Inputs: none.
- Outputs: the list of online mall names.
- Ownership boundary:
  - Owns the online mall name list.
  - MUST NOT select a name.

## Responsibility Boundaries

- Input validation: C-01 only.
- Path checks: C-01 only.
- Column layout and header row content: the row factory of the order sheet type.
- Value generation rules: the row factory of the order sheet type.
- Randomness: C-05 only.
- Date-time text format: C-06 only.
- State application and ERR-005: C-03 only.
- Order grouping and bundle totals: the row factory of the order sheet type.
- Header inclusion decision: C-01 only.
- Serialization and file writing: C-01 only.
- Type-to-factory mapping: C-02 only.

## Data Flow

- Caller -> C-01: order sheet type, count, bundle size, state, header flag, path, filename.
- C-01 -> C-02: order sheet type.
- C-02 -> C-01: row factory.
- C-01 -> row factory: state, count, bundle size.
- C-07 -> C-04: online mall names.
- C-05 -> C-04: random values.
- C-05 -> C-06: random date-time when none is given.
- C-04 -> C-06: date-time values.
- C-06 -> C-04: formatted date-time text.
- Row factory -> C-01: data rows as ordered value lists.
- Row factory -> C-01: header row as ordered column names.
- C-01 -> caller: table, CSV text, or XLSX file.

## Architectural Rules

- Every output request MUST generate data rows exactly once.
- CSV text and XLSX file MUST be serializations of the table produced for that request.
- Each output request MUST use a new row factory instance.
- State MUST be applied to each row definition before order-level copying and bundle totals.
- Bundle totals MUST be computed after order-level copying and MUST overwrite any state value.
- Order-level column values MUST come from the first row of the order after state is applied.
- Data rows MUST leave the row factory as ordered value lists without column names.
- The header row MUST have the same column count and order as every data row.
- A row factory that does not support bundling MUST ignore bundle size and produce count independent rows.
- Adding an order sheet type MUST require only a new row factory and a new entry in C-02.

## Failure Boundaries

- ERR-001: raised by C-01 when count is set; no generation occurs.
- ERR-002: raised by C-01 when bundle size is set; no generation occurs.
- ERR-003: raised by C-01 before generation; no file is written.
- ERR-004: raised by C-01 before generation; no file is written.
- ERR-005: raised by C-03 during generation; generation continues without the unknown key.
- A failure inside C-05 or C-06 propagates to the caller; C-01 MUST NOT write a partial file.
- A failure while writing the XLSX file propagates to the caller unchanged.

## Non-Goals

- No seeded or reproducible generation; the shared value generator has no caller-controlled seed.
- No caching or reuse of generated rows between output requests.
- No streaming or incremental output; the full table is built in memory before serialization.
- No row factory selection outside C-02.
- No validation of state values against column value rules.
- No reading or parsing of existing order sheet files.

## Architectural Invariants

- Each order sheet type maps to exactly one row factory.
- Header row length equals data row length.
- The number of data rows equals count.
- Every order contains bundle size data rows, except the last order, which contains the remainder.
- Every data row of one order holds identical order-level column values.
- Bundle totals of every data row of one order equal the sums over that order.
- A path failure never produces a file.
- Header inclusion never changes data rows.

## Resolved Decisions

- RD-01: Bundle size is validated in both C-01 and C-03.
  - Ambiguity: two owners for one validation.
  - Decision: C-01 owns caller-facing validation; the C-03 check is a guard for direct row factory use and never fires through C-01.
  - Reason: C-01 receives every caller input first.
- RD-02: Count is validated in C-01 only; C-03 accepts any count.
  - Ambiguity: C-03 treats a missing count as 1 and a count of 0 as zero rows.
  - Decision: C-03 count handling is outside the caller contract; only C-01 count rules apply to the caller.
  - Reason: PRODUCT_SPEC.md exposes only C-01 to the caller.
- RD-03: Header row source.
  - Ambiguity: C-03 derives the header from a generated row definition; C-04 returns a fixed list.
  - Decision: each row factory owns its header row; the C-03 derivation is the default for factories that do not define one.
  - Reason: a fixed header does not depend on random generation.
- RD-04: Bundling ownership.
  - Ambiguity: C-03 stores bundle size but does not group rows.
  - Decision: grouping and bundle totals belong to the type-specific row factory.
  - Reason: order-level columns and bundle totals are defined per order sheet type.
- RD-05: ERR-005 frequency.
  - Ambiguity: PRODUCT_SPEC.md requires a warning but does not state how many times.
  - Decision: ERR-005 is raised once per unknown key per row definition.
  - Reason: state is applied per row definition.
- RD-06: Path check timing.
  - Ambiguity: path could be checked when set or when the XLSX file is requested.
  - Decision: C-01 checks the path only when the XLSX file is requested, before generation.
  - Reason: path is unused by table and CSV output.
