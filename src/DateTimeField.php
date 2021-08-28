<?php declare(strict_types=1);

namespace SchemaHelper;

final class DateTimeField extends RangedField
{
    public function __construct(string $name, bool $required=false, bool $nullable=true, ?\DateTime $min = null, ?\DateTime $max = null, $default=null)
    {
        if ($min instanceof \DateTime && $max instanceof \DateTime && $min > $max)
            throw new \InvalidArgumentException('min greater than max');

        parent::__construct($name, FieldType::DATETIME, $required, $nullable, $min, $max, $default);
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        else if ($value instanceof \DateTime)
            $val = $value;
        else if (is_string($value)) {
            try {
                $value = trim($value);
                $val = new \DateTime($value);
            } catch (\Exception $e) {
                return false;
            }
        } else
            return false;

        $min = $this->min();
        $max = $this->max();

        if ($min instanceof \DateTime && $max instanceof \DateTime)
            return ($min <= $val) && ($val <= $max);
        else if ($min instanceof \DateTime)
            return $min <= $val;
        else if ($max instanceof \DateTime)
            return $val <= $max;
        else
            return true;
    }

    public function cast($value): \DateTime
    {
        if (is_null($value))
            throw new \InvalidArgumentException('Invalid value');

        if ($value instanceof \DateTime)
            return $value;
        else if (is_numeric($value)) {
            return new \DateTime('@' . $value);
        }
        else if (is_string($value)) {
            try {
                $value = trim($value);
                if (is_numeric($value))
                    $value = '@' . $value;

                return new \DateTime($value);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Invalid value');
            }
        }

        throw new \InvalidArgumentException('Invalid value');
    }
}
