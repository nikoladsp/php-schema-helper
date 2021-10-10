<?php declare(strict_types=1);

namespace SchemaHelper;

final class EmailField extends Field
{
    public function __construct(string $name, bool $required = false, bool $nullable = true, ?string $default=null)
    {
        parent::__construct(FieldType::EMAIL, $name, $required, $nullable, $default);
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        else if (!is_string($value))
            return false;

        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function dump($value): string
    {
        if (is_null($value))
            throw new \InvalidArgumentException('Invalid value');

        if (is_string($value))
            return trim($value);

        throw new \InvalidArgumentException('Invalid value');
    }
}
