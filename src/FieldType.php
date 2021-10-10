<?php declare(strict_types=1);

namespace SchemaHelper;

final class FieldType
{
    private int $value;
    private string $type;
    private static array $valueMap;

    const INTEGER = 1;
    const STRING = 5;
    const DOUBLE = 10;
    const REGEX = 15;
    const EMAIL = 20;
    const BOOL = 25;
    const DATETIME = 30;
    const SCHEMA = 35;
    const LIST = 40;

    public function __construct($value)
    {
        if (!isset(FieldType::$valueMap)) {
            FieldType::init();
        }

        $valInt = is_int($value);
        $valStr = is_string($value);

        if (!$valInt && !$valStr)
            throw new \InvalidArgumentException('Invalid value');
        else if (is_null($value) || !array_key_exists($value, FieldType::$valueMap)) {
            throw new \InvalidArgumentException('Invalid value');
        } else if ($valInt) {
            $this->value = $value;
            $this->type = FieldType::$valueMap[$value];
        } else if ($valStr) {
            $this->value = FieldType::$valueMap[$value];
            $this->type = $value;
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function type(): string
    {
        return $this->type;
    }

    public static function types(): array
    {
        self::init();
        return array_keys(self::$valueMap);
    }

    private static function init()
    {
        if (!isset(self::$valueMap))
        {
            $constants = (new \ReflectionClass(FieldType::class))->getConstants();
            foreach($constants as $key => $value) {
                self::$valueMap[$key] = $value;
                self::$valueMap[$value] = $key;
            }
        }
    }
}
