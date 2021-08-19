<?php declare(strict_types=1);

namespace SchemaHelper;

final class DateTimeField extends Field
{
    private ?\DateTime $min;
    private ?\DateTime $max;

    public function __construct(string $name, bool $required=false, bool $nullable=true, ?\DateTime $min = null, ?\DateTime $max = null)
    {
        if ($min instanceof \DateTime && $max instanceof \DateTime && $min > $max)
            throw new \InvalidArgumentException('min greater than max');

        parent::__construct($name, FieldType::DATETIME, $required, $nullable);

        $this->min = $min;
        $this->max = $max;
    }

    public function min(): ?\DateTime
    {
        return $this->min;
    }

    public function max(): ?\DateTime
    {
        return $this->max;
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        elseif ($value instanceof \DateTime)
            $val = $value;
        elseif (is_string($value)) {
            try {
                $value = trim($value);
                $val = new \DateTime($value);
            } catch (\Exception $e) {
                return false;
            }
        } else
            return false;

        if ($this->min instanceof \DateTime && $this->max instanceof \DateTime)
            return ($this->min <= $val) && ($val <= $this->max);
        else if ($this->min instanceof \DateTime)
            return $this->min <= $val;
        else if ($this->max instanceof \DateTime)
            return $val <= $this->max;
        else
            return true;
    }
}
