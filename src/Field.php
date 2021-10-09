<?php declare(strict_types=1);

namespace SchemaHelper;

abstract class Field implements Serializable
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
//        else if ($nullable === false && is_null($default))
//            throw new \InvalidArgumentException('nullable is false: default can not be set to null');

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

    protected static function numeric($value) {
        if (!is_numeric($value))
            throw new \InvalidArgumentException('Not a numeric value');

        return $value + 0;
    }
}
