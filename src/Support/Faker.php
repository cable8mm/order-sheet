<?php

namespace Cable8mm\OrderSheet\Support;

use Bezhanov\Faker\Provider\Commerce;
use Bezhanov\Faker\Provider\Device;
use Faker\Factory;
use Faker\Generator;

class Faker
{
    private static ?Generator $instance = null;

    /**
     * Get \Faker\Generator singleton instance
     *
     * This method returns a shared Faker instance that is reused across calls.
     * The instance is initialized with Korean locale and additional providers
     * for commerce and device-related data.
     *
     * @param  ?string  $locale  the locale (default: 'ko_KR')
     * @return Generator The method returns \Faker\Generator singleton instance
     */
    public static function shared(?string $locale = 'ko_KR'): Generator
    {
        if (! isset(self::$instance)) {
            self::$instance = Factory::create($locale);

            self::$instance->addProvider(new Commerce(self::$instance));
            self::$instance->addProvider(new Device(self::$instance));
        }

        return self::$instance;
    }

    /**
     * Create a new Faker instance
     *
     * This method returns a new Faker instance that provides custom methods
     * like dateTime() for Korean online shopping format.
     *
     * @return static The method returns a new Faker instance
     */
    public static function make(): static
    {
        return new self;
    }

    /**
     * Get Korean dateTime format in online shopping industry
     *
     * @return string the format in online shopping
     *
     * @example Faker::make()->dateTime() => "1993-06-09 오후 03:08:34"
     */
    public function dateTime(): string
    {
        $search = ['AM', 'PM'];

        $replace = ['오전', '오후'];

        return str_replace($search, $replace, self::shared()->dateTime()->format('Y-m-d A h:i:s'));
    }
}
