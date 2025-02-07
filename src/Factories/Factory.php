<?php

namespace Cable8mm\OrderSheet\Factories;

abstract class Factory
{
    /**
     * How many is it creating the row
     */
    private int $count;

    /**
     * Change key-value pairs in the definition
     */
    private array $state;

    /**
     * Define a factory definition for online mall companies
     *
     * @return array The method returns a row of a specific order sheet for a company
     */
    abstract public function definition(): array;

    /**
     * Create a new Factory instance
     *
     * @param  int|array|null  $count  The new count
     * @param  array  $state  The new state
     * @return static The method returns a new Factory instance
     */
    public static function make(int|array|null $count = null, array $state = []): static
    {
        return (new static)
            ->count(is_numeric($count) ? $count : null)
            ->state(is_array($count) ? $count : $state);
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

            if (! empty($this->state)) {
                foreach ($this->state as $key => $value) {
                    $record[$key] = $this->{$key} ?? $value;
                }
            }

            $records[] = $record;
        }

        return $records;
    }
}
