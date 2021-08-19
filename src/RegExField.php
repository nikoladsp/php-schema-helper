<?php declare(strict_types=1);

namespace SchemaHelper;

class RegExField extends Field
{
    private string $pattern;

    public function __construct(string $name, string $pattern, bool $required = false, bool $nullable = true)
    {
        if (!$pattern)
            throw new \InvalidArgumentException('pattern is required argument');

        parent::__construct($name, FieldType::REGEX, $required, $nullable);

        $this->pattern = $pattern;
    }

    public function pattern(): string
    {
        return $this->pattern;
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        else if (!is_string($value))
            return false;

        return 1 == preg_match($this->pattern, $value);
    }
}
