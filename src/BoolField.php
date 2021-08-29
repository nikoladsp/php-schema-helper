<?php declare(strict_types=1);

namespace SchemaHelper;

final class BoolField extends Field
{
    private static array $trueVals = array('y', 'yes', 'true', 'on', '1');
    private static array $falseVals = array( 'n', 'no', 'false', 'off', '0');

    public function __construct(string $name, bool $required=false, bool $nullable=true, ?bool $default=null)
    {
        parent::__construct($name, FieldType::BOOL, $required, $nullable, $default);
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();

        static $validVals = array('y', 'yes', 'true', 'on', '1', 'n', 'no', 'false', 'off', '0');

        if (is_bool($value) || is_numeric($value))
            return true;
        else if (is_string($value))
            return in_array(strtolower(trim($value)), $validVals);
        else
            return false;
    }

    public function cast($value): bool
    {
        if (is_null($value))
            throw new \InvalidArgumentException('Invalid value');

        if (is_bool($value) || is_numeric($value))
            return (bool)$value;
        else if (is_string($value)) {
            $value = strtolower(trim($value));
            if (in_array($value, self::$trueVals))
                return true;
            else if (in_array($value, self::$falseVals))
                return false;
        }

        throw new \InvalidArgumentException('Invalid value');
    }
}
