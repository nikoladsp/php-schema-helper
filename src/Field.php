<?php declare(strict_types=1);

namespace SchemaHelper;

abstract class Field implements Serializable
{
    private string $name;
    private FieldType $type;
    private bool $required;
    private bool $nullable;
    private $default;

    protected function __construct($type, string $name='', bool $required=false, bool $nullable=true, $default=null)
    {
        $name = trim($name);
        if (empty($name))
            $name = uniqid('', true);

        $this->type = ($type instanceof FieldType) ? $type : new FieldType($type);
        $this->name = $name;
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

    protected static function numeric($value) {
        if (!is_numeric($value))
            throw new \InvalidArgumentException('Not a numeric value');

        return $value + 0;
    }
}
