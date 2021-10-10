<?php declare(strict_types=1);

namespace SchemaHelper;

final class FieldFactory
{
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
        $default = $params['default'] ?? null;

        static $rangedFields = ['INTEGER', 'STRING', 'DOUBLE', 'DATETIME'];
        if ((!is_null($min) || !is_null($max)) && !in_array($type, $rangedFields))
            throw new \InvalidArgumentException($type . ' does not support range');

        static $formatFields = ['DATETIME'];
        if (!is_null($format) && !in_array($type, $formatFields))
            throw new \InvalidArgumentException($type . ' does not support format');

        switch ($type) {
            case 'INTEGER': return new IntField($name, $required, $nullable, $min, $max, $default);
            case 'STRING': return new StringField($name, $required, $nullable, $min, $max, $default);
            case 'DOUBLE': return new DoubleField($name, $required, $nullable, $min, $max, $default);
            case 'REGEX': return new RegExField($name, $params['pattern'] ?? null, $required, $nullable, $default);
            case 'EMAIL': return new EmailField($name, $required, $nullable, $default);
            case 'BOOL': return new BoolField($name, $required, $nullable, $default);
            case 'DATETIME': return new DateTimeField($name, $format ?? 'c', $required, $nullable, $min, $max, $default);
            default:
            {
                $schemaName = $params['type'] ?? null;
                if (!is_null($schemaName) && class_exists($schemaName)) {
                    return new SchemaField(new $schemaName, $name, $required, $nullable, $default);
                }

                throw new \InvalidArgumentException('Invalid type');
            }
        }
    }
}
