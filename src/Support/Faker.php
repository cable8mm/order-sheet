<?php

namespace Cable8mm\OrderSheet\Support;

class Faker
{
    /**
     * @var \Faker\Generator
     */
    private static $instance;

    /**
     * Get \Faker\Generator singleton instance
     *
     * @param  ?string  $locale  the locale
     * @return \Faker\Generator The method returns \Faker\Generator singleton instance
     */
    public static function shared(?string $locale = 'ko_KR'): \Faker\Generator
    {
        if (! isset(self::$instance)) {
            self::$instance = \Faker\Factory::create($locale);

            self::$instance->addProvider(new \Bezhanov\Faker\Provider\Commerce(self::$instance));
            self::$instance->addProvider(new \Bezhanov\Faker\Provider\Device(self::$instance));
        }

        return self::$instance;
    }

    /**
     * Faker factory method
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
