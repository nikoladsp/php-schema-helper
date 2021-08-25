<?php declare(strict_types=1);

namespace SchemaHelper;

abstract class Field
{
    private string $name;
    private FieldType $type;
    private bool $required;
    private bool $nullable;
    private $default;

    protected function __construct(string $name, $type, bool $required=false, bool $nullable=true, $default=null)
    {
        if (!$name)
            throw new \InvalidArgumentException('name is required argument');

        $this->name = $name;
        $this->type = ($type instanceof FieldType) ? $type : new FieldType($type);
        $this->required = $required;
        $this->nullable = $nullable;
        $this->default = $default;
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

    public function default()
    {
        return $this->default;
    }

    abstract public function validate($value): bool;

    abstract public function cast($value);

    protected static function numeric($value) {
        if (!is_numeric($value))
            throw new \InvalidArgumentException('Not a numeric value');

        return $value + 0;
    }
}
