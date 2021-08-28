<?php declare(strict_types=1);

namespace SchemaHelper;

class Registry
{
    private static ?Registry $instance = null;
    private array $schemaData = array();

    private function __construct()
    {

    }

    public static function instance(): Registry
    {
        if (is_null(self::$instance))
            self::$instance = new Registry();

        return self::$instance;
    }

    public function registered(string $schema): bool
    {
        return array_key_exists($schema, $this->schemaData);
    }

    public function add(string $schema, array $model, array $fields)
    {
        if (isset($this->schemaData[$schema]))
            throw new \InvalidArgumentException($schema . ' already has been registered');
        else if (empty($model))
            throw new \InvalidArgumentException($schema . ' model is empty');
        else if (empty($fields))
            throw new \InvalidArgumentException($schema . ' fields are empty');

        $this->schemaData[$schema]['model'] = $model;
        $this->schemaData[$schema]['fields'] = $fields;
    }

    public function remove(string $schema)
    {
        if (!isset($this->schemaData[$schema]))
            throw new \InvalidArgumentException($schema . ' has not been registered');

        unset($this->schemaData[$schema]);
    }

    public function count(): int
    {
        return count($this->schemaData);
    }

    public function clear()
    {
        $this->schemaData = array();
    }

    public function get(string $schema): array
    {
        if (!isset($this->schemaData[$schema]))
            throw new \InvalidArgumentException($schema . ' has not been registered');

        return $this->schemaData[$schema];
    }

    public function model(string $schema): array
    {
        if (!isset($this->schemaData[$schema]))
            throw new \InvalidArgumentException($schema . ' has not been registered');

        return $this->schemaData[$schema]['model'];
    }

    public function fields(string $schema): array
    {
        if (!isset($this->schemaData[$schema]))
            throw new \InvalidArgumentException($schema . ' has not been registered');

        return $this->schemaData[$schema]['fields'];
    }
}
