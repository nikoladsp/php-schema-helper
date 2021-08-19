<?php declare(strict_types=1);

namespace SchemaHelper;

final class BoolField extends Field
{
    public function __construct(string $name, bool $required=false, bool $nullable=true)
    {
        parent::__construct($name, FieldType::BOOL, $required, $nullable);
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();

        if (is_bool($value) || is_numeric($value))
            return true;
        else if (is_string($value))
            return in_array(strtolower($value), array('y', 'yes', 'true', 'on', '1', 'n', 'no', 'false', 'off', '0'));
        else
            return false;
    }
}
