<?php declare(strict_types=1);

namespace SchemaHelper;

abstract class Schema implements Serializable
{
    private static ?string $model = null;

    public function __construct()
    {
        $this->init();
    }

    private static function objToArray($obj) {
        if (is_object($obj))
            $obj = get_object_vars($obj);

        return is_array($obj) ? array_map(__METHOD__, $obj) : $obj;
    }

    public function model(): string
    {
        $reg = Registry::instance();
        $className = get_class($this);

        return $reg->modelClass($className);
    }

    private static function getModelClass(\ReflectionClass $rc): string
    {
        do {
            if (!$rc->hasProperty('model'))
                continue;

            $property = $rc->getProperty('model');
            if (!is_null($property)) {
                $property->setAccessible(true);
                $modelClass = $property->getValue();

                if (is_null($modelClass))
                    continue;
                else if (!class_exists($modelClass))
                    throw new \InvalidArgumentException('Class ' . $modelClass . ' does not exist');

                return $modelClass;
            }
        } while ($rc = $rc->getParentClass());

        throw new \InvalidArgumentException('No model property defined');
    }

    private static function getModelProperties(string $model): array
    {
        $rc = new \ReflectionClass($model);
        $properties = $rc->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach($properties as $i => $item)
            $properties[$i] = $item->name;

        return $properties;
    }

    private function init()
    {
        $className = get_class($this);
        $reg = Registry::instance();
        if ($reg->registered($className))
            return;

        $rc = new \ReflectionClass($className);

        $constants = $rc->getConstants();
        $model = self::getModelClass($rc);
        $fields = array();

        foreach ($constants as $key => $params) {
            $name = $params['name'] ?? strtolower($key);
            $fields[$name] = FieldFactory::create($params, $name);
        }

        $reg->add($className, $model, self::getModelProperties($model), $fields);
    }

    public function validate($value): bool
    {
        if (! is_array($value) && ! ($value instanceof Model) )
            return false;

        $reg = Registry::instance();
        $className = get_class($this);
        if (!$reg->registered($className))
            throw new \InvalidArgumentException($className . ' have not been registered');

        $schemaData = $reg->get($className);
        $model = $schemaData['model'];
        $fields = $schemaData['fields'];

        if ($value instanceof Model)
            $value = self::objToArray($value);

        foreach ($model as $attr) {
            if (array_key_exists($attr, $fields)) {
                $field = $fields[$attr];
                if (!$field->validate($value[$attr]))
                    return false;
            }
        }

        return true;
    }

    public function dump($instance, bool $stopOnError = false): array
    {
        if (!$instance instanceof Model)
            throw new \InvalidArgumentException('Object not instance of Model');

        $reg = Registry::instance();
        $className = get_class($this);
        if (!$reg->registered($className))
            throw new \InvalidArgumentException($className . ' have not been registered');

        $schemaData = $reg->get($className);
        $model = $schemaData['model'];
        $fields = $schemaData['fields'];

        $result = array();
        if ($stopOnError) {
            foreach ($model as $attr) {
                if (array_key_exists($attr, $fields)) {
                    $field = $fields[$attr];
                    $result[$attr] = $field->dump($instance->$attr);
                }
            }
        } else {
            $errors = array();
            foreach ($model as $attr) {
                if (array_key_exists($attr, $fields)) {
                    try {
                        $field = $fields[$attr];
                        $result[$attr] = $field->dump($instance->$attr);
                    } catch (\InvalidArgumentException $e) {
                        $errors[$attr] = $e->getMessage();
                    }
                }
            }

            if (!empty($errors))
                throw new \InvalidArgumentException(json_encode($errors));
        }

        return $result;
    }

    public function dumpToJson(Model $instance, bool $stopOnError = false): string
    {
        return json_encode($this->dump($instance, $stopOnError));
    }
}
