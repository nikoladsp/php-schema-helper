<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\Registry;
use \SchemaHelper\Schema;
use \SchemaHelper\Model;
use \SchemaHelper\FieldType;
use \SchemaHelper\SchemaField;

class TestModel extends Model
{
    public int $id;
    public string $username;
    public string $description;
}

class TestSchema extends Schema
{
    private static string $model = TestModel::class;

    const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
    const username = array('type' => 'string', 'required' => true, 'nullable' => false);
    const description = array('type' => 'string', 'required' => false, 'nullable' => true);
}

class SchemaFieldTest extends \PHPUnit\Framework\TestCase
{
    public function test_type()
    {
        $field = new SchemaField('token', new TestSchema());
        $this->assertEquals(new FieldType('SCHEMA'), $field->type());
    }

    public function test_pattern()
    {
        $field = new SchemaField('token', new TestSchema());
        $this->assertInstanceOf(TestSchema::class, $field->schema());
    }

    public function test_construct_no_name()
    {
        $this->expectException(\InvalidArgumentException::class);
        new SchemaField('', new TestSchema());
    }

    public function test_nullable()
    {
        $field = new SchemaField('token', new TestSchema(), false, true);
        $this->assertTrue($field->validate(null));

        $field = new SchemaField('token', new TestSchema(), false, false);
        $this->assertFalse($field->validate(null));
    }

    public function test_validate_unsupported()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->assertFalse($field->validate(new stdClass()));
        $this->assertFalse($field->validate(121));
        $this->assertFalse($field->validate('This is so wrong!'));
    }

    public function test_validate_not_registered()
    {
        $field = new SchemaField('token', new TestSchema());
        $reg = Registry::instance();
        $reg->remove(TestSchema::class);

        $this->expectException(\InvalidArgumentException::class);
        $field->validate(array("id" => 1, "username" => "testuser"));
    }

    public function test_valid_schema()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->assertTrue($field->validate(array("id" => 1, "username" => "testuser")));
        $this->assertTrue($field->validate(array("id" => 1, "username" => "testuser", "description" => "test desc...")));

        $model = new TestModel();
        $model->id = 1;
        $model->username = "testuser";

        $this->assertTrue($field->validate($model));

        $model = new TestModel();
        $model->id = 1;
        $model->username = "testuser";
        $model->description = "test desc...";

        $this->assertTrue($field->validate($model));
    }

    public function test_invalid_schema()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->assertFalse($field->validate(array("username" => "testuser")));
        $this->assertFalse($field->validate(array("username" => "testuser", "description" => "test desc...")));

        $this->assertFalse($field->validate(array("id" => 1)));
        $this->assertFalse($field->validate(array("id" => 1, "description" => "test desc...")));

        $model = new TestModel();
        $model->username = "testuser";
        $model->description = "test desc...";

        $this->assertFalse($field->validate($model));

        $model = new TestModel();
        $model->id = 1;
        $model->description = "test desc...";

        $this->assertFalse($field->validate($model));
    }

    public function test_cast_null()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(null);
    }

    public function test_cast_empty()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->expectException(\InvalidArgumentException::class);
        $field->cast('');
    }

    public function test_cast_invalid_bool_true()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(true);
    }

    public function test_cast_invalid_bool_false()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(false);
    }

    public function test_cast_int()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(+1);
    }

    public function test_cast_float()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(2.056);
    }

    public function test_cast_invalid_object()
    {
        $field = new SchemaField('token', new TestSchema());

        $this->expectException(\InvalidArgumentException::class);
        $field->cast(new stdClass());
    }

    public function test_cast_valid()
    {
        $field = new SchemaField('token', new TestSchema());

        $model = new TestModel();
        $model->id = 3;
        $model->username = 'testuser';
        $model->description = 'desc...';

        $casted = $field->cast($model);
        $this->assertEquals($model->id, $casted->id);
        $this->assertEquals($model->username, $casted->username);
        $this->assertEquals($model->description, $casted->description);

        $model = array('id' => 3, 'username' => 'testuser', 'description' => 'desc...');
        $casted = $field->cast($model);
        $this->assertEquals($model['id'], $casted->id);
        $this->assertEquals($model['username'], $casted->username);
        $this->assertEquals($model['description'], $casted->description);
    }
}
