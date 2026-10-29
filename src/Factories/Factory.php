<?php

namespace Cable8mm\OrderSheet\Factories;

abstract class Factory
{
    /**
     * How many is it creating the row
     */
    protected int $count = 1;

    /**
     * The number of order lines to generate for each order.
     */
    protected int $bundleSize = 1;

    /**
     * Change key-value pairs in the definition
     */
    protected array $state = [];

    /**
     * Define a factory definition for online mall companies
     *
     * @return array The method returns a row of a specific order sheet for a company
     */
    abstract public function definition(): array;

    /**
     * Create a new Factory instance
     *
     * @return static The method returns a new Factory instance
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * Update the count of the returned definitions
     *
     * @param  int|null  $count  The new count
     * @return static The method returns the new count
     */
    public function count(?int $count = null): static
    {
        $this->count = $count ?? 1;

        return $this;
    }

    /**
     * Set the number of rows to create for each order.
     *
     * Factories that support grouped orders use this value to share order-level
     * fields and calculate bundle totals.
     *
     * @throws \InvalidArgumentException If the bundle size is less than 1
     */
    public function bundle(int $bundleSize): static
    {
        if ($bundleSize < 1) {
            throw new \InvalidArgumentException('Bundle size must be at least 1');
        }

        $this->bundleSize = $bundleSize;

        return $this;
    }

    /**
     * Update the state of the returned definitions
     *
     * @param  array  $state  The new state
     * @return static The method returns the new state
     */
    public function state(array $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Create definition(s) with the given state and count
     *
     * @return array The method returns the definition with the given state and count
     */
    public function create(): array
    {
        $records = [];

        for ($i = 0; $i < $this->count; $i++) {
            $record = $this->definition();

            $records[] = array_values($this->applyState($record));
        }

        return $records;
    }

    /**
     * Get header cells
     *
     * @return array<string> The header cells
     */
    public function header(): array
    {
        return array_keys($this->definition());
    }

    /**
     * Apply configured values to a generated record.
     *
     * @param  array<string, mixed>  $record
     * @return array<string, mixed>
     */
    protected function applyState(array $record): array
    {
        foreach ($this->state as $key => $value) {
            if (array_key_exists($key, $record)) {
                $record[$key] = $value;
            } else {
                trigger_error(
                    sprintf('State key "%s" does not exist in definition and will be ignored', $key),
                    E_USER_WARNING
                );
            }
        }

        return $record;
    }
}
