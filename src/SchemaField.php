<?php declare(strict_types=1);

namespace SchemaHelper;

final class SchemaField extends Field
{
    private Schema $schema;

    public function __construct(string $name, Schema $schema, bool $required = false, bool $nullable = true, ?Schema $default = null)
    {
        parent::__construct($name, FieldType::SCHEMA, $required, $nullable, $default);

        $this->schema = $schema;
    }

    public function schema(): Schema
    {
        return $this->schema;
    }

    private static function objToArray($obj) {
        if (is_object($obj))
            $obj = get_object_vars($obj);

        return is_array($obj) ? array_map(__METHOD__, $obj) : $obj;
    }

    public function validate($value): bool
    {
        if (is_null($value))
            return $this->nullable();
        else if (! is_array($value) && ! ($value instanceof Model) )
            return false;

        $reg = Registry::instance();
        $className = get_class($this->schema);
        if (!$reg->registered($className))
            throw new \InvalidArgumentException($className . ' have not been registered');

        $schemaData = $reg->get($className);
        $fields = $schemaData['fields'];

        if ($value instanceof Model)
            $value = self::objToArray($value);

        $valid = true;
        foreach ($fields as $field) {
            $val = $value[$field->name()] ?? null;
            if (!$field->validate($val)) {
                $valid = false;
                break;
            };
        }

        return $valid;
    }

    public function dump($value): Model
    {
        if (is_null($value))
            throw new \InvalidArgumentException('Invalid value');

        if ($value instanceof Model)
            return $value;
        else if (is_array($value)) {
            $className = $this->schema->model();
            $object = new $className;
            foreach ($value as $key => $value)
                $object->$key = $value;

            return $object;
        }

        throw new \InvalidArgumentException('Invalid value');
    }
}

