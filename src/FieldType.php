<?php declare(strict_types=1);

namespace SchemaHelper;

final class FieldType
{
    private int $value;
    private string $type;
    private static array $valueMap;

    const INTEGER = 1;
    const STRING = 2;
    const DOUBLE = 3;
    const REGEX = 4;
    const EMAIL = 5;
    const BOOL = 6;
    const DATETIME = 7;

    public function __construct($value)
    {
        if (!isset(FieldType::$valueMap)) {
            FieldType::init();
        }

        if (is_null($value) || !array_key_exists($value, FieldType::$valueMap)) {
            throw new \InvalidArgumentException('Invalid value');
        } else if (is_int($value)) {
            $this->value = $value;
            $this->type = FieldType::$valueMap[$value];
        } else if (is_string($value)) {
            $this->value = FieldType::$valueMap[$value];
            $this->type = $value;
        } else {
            throw new \InvalidArgumentException('Invalid value');
        }
    }

    public function value(): int {
        return $this->value;
    }

    public function type(): string {
        return $this->type;
    }

    private static function init()
    {
        if (!isset(FieldType::$valueMap))
        {
            $constants = (new \ReflectionClass(FieldType::class))->getConstants();
            if (count($constants) !== count(array_unique(array_values($constants))))
                throw new \InvalidArgumentException('Duplicate constant value');

            foreach($constants as $key => $value) {
                FieldType::$valueMap[$key] = $value;
                FieldType::$valueMap[$value] = $key;
            }
        }
    }
}
