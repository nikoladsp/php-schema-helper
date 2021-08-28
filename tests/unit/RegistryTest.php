<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\Registry;

class RegistryTest extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        Registry::instance()->clear();
    }

    public function test_construct()
    {
        $reg1 = Registry::instance();
        $reg2 = Registry::instance();

        $this->assertTrue($reg1 instanceof Registry);
        $this->assertEquals($reg1, $reg2);
    }

    public function test_registered()
    {
        $reg = Registry::instance();

        $className = 'MySchemaClass';
        $this->assertFalse($reg->registered($className));

        $reg->add($className, array('model1'), array('field1'));
        $this->assertTrue($reg->registered($className));
    }

    public function test_add_not_registered()
    {
        $reg = Registry::instance();

        $className = 'MySchemaClass';
        $model = array('model1', 'model2');
        $fields = array('field1', 'field2');

        $reg->add($className, $model, $fields);
        $this->assertEquals($model, $reg->model($className));
        $this->assertEquals($fields, $reg->fields($className));
    }

    public function test_add_already_registered()
    {
        $reg = Registry::instance();

        $className = 'MySchemaClass';
        $model = array('model1', 'model2');
        $fields = array('field1', 'field2');
        $reg->add($className, $model, $fields);

        $this->expectException(\InvalidArgumentException::class);
        $reg->add($className, array('model1'), array('field1'));
    }

    public function test_remove_not_registered()
    {
        $reg = Registry::instance();

        $this->expectException(\InvalidArgumentException::class);
        $reg->remove('MySchemaClass');
    }

    public function test_remove_registered()
    {
        $reg = Registry::instance();
        $className = 'MySchemaClass';

        $reg->add($className, array('model1'), array('field1'));
        $this->assertTrue($reg->registered($className));

        $reg->remove($className);
        $this->assertFalse($reg->registered($className));
    }

    public function test_count()
    {
        $reg = Registry::instance();
        $this->assertEquals(0, $reg->count());

        $reg->add('MySchemaClass', array('model1'), array('field1'));
        $this->assertEquals(1, $reg->count());
    }

    public function test_clear()
    {
        $reg = Registry::instance();
        $className1 = 'MySchemaClass1';
        $className2 = 'MySchemaClass2';

        $reg->add($className1, array('model1'), array('field1'));
        $reg->add($className2, array('model2'), array('field2'));

        $this->assertEquals(2, count($reg->get($className1)));
        $this->assertEquals(2, count($reg->get($className2)));

        $reg->clear();

        $this->assertFalse($reg->registered($className1));
        $this->assertFalse($reg->registered($className2));
        $this->assertEquals(0, $reg->count());
    }

    public function test_add_empty_model()
    {
        $reg = Registry::instance();

        $this->expectException(\InvalidArgumentException::class);
        $reg->add('MySchemaClass', array(), array('field1'));
    }

    public function test_add_empty_fields()
    {
        $reg = Registry::instance();

        $this->expectException(\InvalidArgumentException::class);
        $reg->add('MySchemaClass', array('model1'), array());
    }

    public function test_get_not_registered()
    {
        $reg = Registry::instance();

        $this->expectException(\InvalidArgumentException::class);
        $reg->get('MySchemaClass');
    }

    public function test_get_registered()
    {
        $reg = Registry::instance();

        $className = 'MySchemaClass';
        $model = array('model1', 'model2');
        $fields = array('field1', 'field2');
        $reg->add($className, $model, $fields);

        $schemaData = $reg->get($className);
        $this->assertEquals(2, count($schemaData));
        $this->assertEquals($model, $schemaData['model']);
        $this->assertEquals($fields, $schemaData['fields']);
    }

    public function test_model_not_registered()
    {
        $reg = Registry::instance();

        $this->expectException(\InvalidArgumentException::class);
        $reg->model('MySchemaClass');
    }

    public function test_model_registered()
    {
        $reg = Registry::instance();

        $className = 'MySchemaClass';
        $model = array('model1', 'model2');
        $fields = array('field1', 'field2');
        $reg->add($className, $model, $fields);

        $this->assertEquals($model, $reg->model($className));
    }

    public function test_fields_not_registered()
    {
        $reg = Registry::instance();

        $this->expectException(\InvalidArgumentException::class);
        $reg->fields('MySchemaClass');
    }

    public function test_fields_registered()
    {
        $reg = Registry::instance();

        $className = 'MySchemaClass';
        $model = array('model1', 'model2');
        $fields = array('field1', 'field2');
        $reg->add($className, $model, $fields);

        $this->assertEquals($fields, $reg->fields($className));
    }
}
