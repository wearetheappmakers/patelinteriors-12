<?php

namespace App\Traits;

use DateTimeInterface;

trait SerializeDateTrait
{
    /**
     * Prepare a date for array / JSON serialization (keeps Laravel 5.8 format).
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

