<?php declare(strict_types=1);

namespace SchemaHelper;

final class DoubleField extends Field
{
    private ?float $min;
    private ?float $max;

    public function __construct(string $name, bool $required=false, bool $nullable=true, ?float $min = null, ?float $max = null)
    {
        if (is_numeric($min) && is_numeric($max) && $min > $max)
            throw new \InvalidArgumentException('min greater than max');

        parent::__construct($name, FieldType::DOUBLE, $required, $nullable);

        $this->min = $min;
        $this->max = $max;
    }

    public function min(): ?float
    {
        return $this->min;
    }

    public function max(): ?float
    {
        return $this->max;
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();

        try {
            $val = is_float($value) ? $value : $this->numeric($value);
            if (!is_float($val) && !is_int($val))
                return false;
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        if (is_float($this->min) && is_float($this->max))
            return ($this->min <= $val) && ($val <= $this->max);
        else if (is_float($this->min))
            return $this->min <= $val;
        else if (is_float($this->max))
            return $val <= $this->max;
        else
            return true;
    }
}
