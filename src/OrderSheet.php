<?php

namespace Cable8mm\OrderSheet;

use Cable8mm\OrderSheet\Enums\OrderSheetType;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Main entry point for creating new order sheets
 */
class OrderSheet
{
    /**
     * The number of rows in the order sheet
     */
    private int $count;

    /**
     * The state of the order sheet
     */
    private array $state = [];

    /**
     * The path to save the order sheet
     */
    private string $path;

    /**
     * Whether to include the header in the order sheet
     */
    private bool $header = false;

    /**
     * The factory class for creating order sheet data
     */
    private string $factoryClass;

    /**
     * Constructor
     */
    private function __construct(
        private OrderSheetType $orderSheetType
    ) {
        $this->factoryClass = $this->orderSheetType->factoryClass();
    }

    /**
     * Setter for $count
     *
     * @param  int  $count  The number of rows in the order sheet
     * @return static The method returns self instance
     */
    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Setter for $state
     *
     * @param  array  $state  The state of the order sheet
     * @return static The method returns self instance
     */
    public function state(array $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Setter for $path
     *
     * @param  string  $path  The path to save the order sheet
     * @return static The method returns self instance
     */
    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Setter for $header
     *
     * @param  bool  $header  Whether to include the header in the order sheet
     * @return static The method returns self instance
     */
    public function header(bool $header = true): static
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Export the order sheet data to array or CSV
     *
     * @return array The method returns the order sheet data as array or CSV string
     */
    public function toArray(): array
    {
        $rows = $this->factoryClass::make()->state($this->state)->count($this->count)->create();

        if ($this->header) {
            array_unshift($rows, $this->factoryClass::make()->header());
        }

        return $rows;
    }

    /**
     * Export the order sheet data to CSV
     *
     * @return string The method returns the order sheet data as CSV string
     */
    public function csv(): string
    {
        $writer = Writer::createFromString();
        $writer->insertAll($this->toArray());

        return $writer->toString();
    }

    /**
     * Export the order sheet data to XLSX
     *
     * @param  string  $filename  A filename to create
     */
    public function xlsx(string $filename = 'order_sheet.xlsx'): void
    {
        $spreadsheet = new Spreadsheet;
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->fromArray($this->toArray());

        $writer = new Xlsx($spreadsheet);

        $writer->save($this->path.DIRECTORY_SEPARATOR.$filename);
    }

    /**
     * Magic method to convert the order sheet type to string
     *
     * @return string The method returns the order sheet type as string
     */
    public function __toString()
    {
        return $this->orderSheetType->value;
    }

    /**
     * Factory method to create an instance of OrderSheet
     *
     * @param  OrderSheetType  $orderSheetType  The order sheet type
     * @return static The method returns the OrderSheet instance
     */
    public static function of(OrderSheetType $orderSheetType): static
    {
        return new static($orderSheetType);
    }
}
