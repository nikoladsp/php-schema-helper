<?php declare(strict_types=1);

namespace SchemaHelper;

abstract class Field
{
    private string $name;
    private FieldType $type;
    private bool $required;
    private bool $nullable;

    protected function __construct(string $name, $type, bool $required=false, bool $nullable=true)
    {
        if (!$name)
            throw new \InvalidArgumentException('name is required argument');

        $this->name = $name;
        $this->type = ($type instanceof FieldType) ? $type : new FieldType($type);
        $this->required = $required;
        $this->nullable = $nullable;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): FieldType
    {
        return $this->type;
    }

    public function required(): bool
    {
        return $this->required;
    }

    public function nullable(): bool
    {
        return $this->nullable;
    }

    abstract public function validate($value): bool;

    protected static function numeric($value) {
        if (!is_numeric($value))
            throw new \InvalidArgumentException('Not a numeric value');

        return $value + 0;
    }
}
