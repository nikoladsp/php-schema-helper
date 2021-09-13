<?php declare(strict_types=1);

namespace SchemaHelper;

abstract class Schema
{
    private static ?string $model = null;

    public function __construct()
    {
        $this->init();
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

//        $sc = $rc->isSubclassOf('\SchemaHelper\Schema');

        $basicTypes = FieldType::types();
        $constants = $rc->getConstants();
        $model = self::getModelClass($rc);
        $fields = array();

        foreach ($constants as $key => $params) {
            $name = $params['name'] ?? strtolower($key);
            $fields[$name] = FieldFactory::create($params, $name);

//            if (array_key_exists('type', $params) && !array_key_exists($params['type'], $basicTypes)) {
//                $fields[$name] = ;
//            } else {
//                $fields[$name] = FieldFactory::create($params, $name);
//            }
        }

        $reg->add($className, $model, self::getModelProperties($model), $fields);
    }

    public function dump(Model $instance, bool $stopOnError = false): array
    {
        if (is_null($instance))
            throw new \InvalidArgumentException('Object can not be null');
        else if (!$instance instanceof Model)
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
                    $result[$attr] = $field->cast($instance->$attr);
                }
            }
        } else {
            $errors = array();
            foreach ($model as $attr) {
                if (array_key_exists($attr, $fields)) {
                    try {
                        $field = $fields[$attr];
                        $result[$attr] = $field->cast($instance->$attr);
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
