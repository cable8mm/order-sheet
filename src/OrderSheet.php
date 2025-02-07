<?php

namespace Cable8mm\OrderSheet;

use Cable8mm\OrderSheet\Enums\OrderSheetType;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderSheet
{
    private int $count;

    private array $state = [];

    private string $path;

    private bool $header = false;

    private string $factoryClass;

    private function __construct(
        private OrderSheetType $orderSheetType
    ) {
        $this->factoryClass = $this->orderSheetType->factoryClass();
    }

    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function state(array $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function path(string $path): static
    {
        $this->path = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$path);

        return $this;
    }

    public function header(bool $header = true): static
    {
        $this->header = $header;

        return $this;
    }

    public function toArray(): array
    {
        $rows = $this->factoryClass::make()->state($this->state)->count($this->count)->create();

        if ($this->header) {
            array_unshift($rows, $this->factoryClass::make()->header());
        }

        return $rows;
    }

    public function csv(): string
    {
        $writer = Writer::createFromString();
        $writer->insertAll($this->toArray());

        return $writer->toString();
    }

    public function xlsx(bool $new = true): void
    {
        $spreadsheet = new Spreadsheet;
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->fromArray($this->toArray());

        $writer = new Xlsx($spreadsheet);

        $writer->save($this->path.DIRECTORY_SEPARATOR.'order_sheet.xlsx');
    }

    public function __toString()
    {
        return $this->orderSheetType->value;
    }

    public static function of(OrderSheetType $orderSheetType): static
    {
        return new static($orderSheetType);
    }
}
