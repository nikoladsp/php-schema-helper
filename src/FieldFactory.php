<?php declare(strict_types=1);

namespace SchemaHelper;

final class FieldFactory
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function create(array $params, string $defaultName = null): Field
    {
        $type = strtoupper($params['type'] ?? 'string');
        $name = $params['name'] ?? ($defaultName ? strtolower($defaultName) : null);
        if (is_null($name))
            throw new \InvalidArgumentException('Name is missing');

        $required = $params['required'] ?? false;
        $nullable = $params['nullable'] ?? true;
        $format = $params['format'] ?? null;
        $min = $params['min'] ?? null;
        $max = $params['max'] ?? null;

        static $rangedFields = ['INTEGER', 'STRING', 'DOUBLE', 'DATETIME'];
        if ((!is_null($min) || !is_null($max)) && !in_array($type, $rangedFields))
            throw new \InvalidArgumentException($type . ' does not support range');

        static $formatFields = ['DATETIME'];
        if (!is_null($format) && !in_array($type, $formatFields))
            throw new \InvalidArgumentException($type . ' does not support format');

        switch ($type) {
            case 'INTEGER': return new IntField($name, $required, $nullable, $min, $max);
            case 'STRING': return new StringField($name, $required, $nullable, $min, $max);
            case 'DOUBLE': return new DoubleField($name, $required, $nullable, $min, $max);
            case 'REGEX': return new RegExField($name, $params['pattern'] ?? null, $required, $nullable);
            case 'EMAIL': return new EmailField($name, $required, $nullable);
            case 'BOOL': return new BoolField($name, $required, $nullable);
            case 'DATETIME': return new DateTimeField($name, $format ?? 'c', $required, $nullable, $min, $max);
            default:
                throw new \InvalidArgumentException('Invalid type');
        }
    }
}
