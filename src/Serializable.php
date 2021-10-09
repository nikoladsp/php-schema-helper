<?php declare(strict_types=1);

namespace SchemaHelper;

interface Serializable
{
    public function validate($value): bool;

    public function dump($value);
}
