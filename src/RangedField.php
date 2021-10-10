<?php declare(strict_types=1);

namespace SchemaHelper;

abstract class RangedField extends Field
{
    private $min;
    private $max;

    protected function __construct($type, string $name, bool $required = false, bool $nullable = true, $min = null, $max = null, $default = null)
    {
        if (is_numeric($min) && is_numeric($max) && $min > $max)
            throw new \InvalidArgumentException('min greater than max');

        parent::__construct($type, $name, $required, $nullable, $default);

        $this->min = $min;
        $this->max = $max;
    }

    public function min()
    {
        return $this->min;
    }

    public function max()
    {
        return $this->max;
    }
}
