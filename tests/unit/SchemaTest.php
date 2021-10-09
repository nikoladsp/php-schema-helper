<?php declare(strict_types=1);

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use \SchemaHelper\Registry;
use \SchemaHelper\Schema;
use \SchemaHelper\Model;

class TestUserModel extends Model
{
    public $id;
    public $username;
}

class TestTokenModel extends Model
{
    public $id;
    public $user_id;
    public $value;
}

class TestCompositeSchema extends Schema
{
    private static string $model = TestTokenModel::class;

    const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
    const user_id = array('type' => 'integer', 'required' => true, 'nullable' => false);
    const value = array('type' => 'string', 'required' => true, 'nullable' => false);
};

class TestUserExtendedModel extends Model
{
    public $id;
    public $username;
    public $token;
}

class SchemaTest extends \PHPUnit\Framework\TestCase
{
    public function test_no_model()
    {
        $this->expectException(\InvalidArgumentException::class);
        new class() extends Schema {};
    }

    public function test_invalid_model()
    {
        $this->expectException(\InvalidArgumentException::class);
        new class() extends Schema {
            private static string $model = '';
        };
    }

    public function test_empty_schema()
    {
        $this->expectException(\InvalidArgumentException::class);
        new class() extends Schema {
            private static string $model = TestUserModel::class;
        };
    }

    public function test_schema_not_registered()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();

        $reg = Registry::instance();
        $reg->remove(get_class($schema));

        $this->expectException(\InvalidArgumentException::class);
        $schema->dump($model);
    }

    public function test_dump_invalid_model()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };
        $schema = new $schemaClass();
        $model = 77;

        $this->expectException(\InvalidArgumentException::class);
        $schema->dump($model);
    }

    public function test_dump_composite()
    {
        $token = new TestTokenModel();
        $token->id = 1;
        $token->user_id = 2;
        $token->value = 'tokenvalue';

        $user = new TestUserExtendedModel();
        $user->id = 2;
        $user->username = 'testuser';
        $user->token = $token;

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserExtendedModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
            const token = array('type' => TestCompositeSchema::class, 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();

        // test dumping to object
        $res = $schema->dump($user);
        $this->assertEquals(array('id' => $user->id, 'username' => $user->username, 'token' => $user->token), $res);

        // test dumping to JSON string
        $res = $schema->dumpToJson($user);
        $this->assertEquals('{"id":2,"username":"testuser","token":{"id":1,"user_id":2,"value":"tokenvalue"}}', $res);
    }

    public function test_dump_all()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();
        $res = $schema->dump($model);

        $this->assertEquals(array('id' => $model->id, 'username' => $model->username), $res);
    }

    public function test_dump_stop_on_first_error()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'string', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();

        $this->expectException(\InvalidArgumentException::class);
        $schema->dump($model, true);
    }

    public function test_dump_stop_on_last_error()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'integer', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();

        $this->expectException(\InvalidArgumentException::class);
        $schema->dump($model, true);
    }

    public function test_dump_no_stop_failed()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'string', 'required' => true, 'nullable' => false);
            const username = array('type' => 'integer', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();

        $this->expectExceptionMessage('{"id":"Invalid value","username":"Not a numeric value"}');
        $schema->dump($model, false);
    }

    public function test_dump_to_json()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();
        $res = json_decode($schema->dumpToJson($model));

        $this->assertEquals($res->id, $model->id);
        $this->assertEquals($res->username, $model->username);
    }

    public function test_validate_unsupported()
    {
        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();

        $this->assertFalse($schema->validate(new stdClass()));
        $this->assertFalse($schema->validate(121));
        $this->assertFalse($schema->validate('This is so wrong!'));
    }

    public function test_validate_not_registered()
    {
        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();

        $reg = Registry::instance();
        $reg->remove(get_class($schema));

        $this->expectException(\InvalidArgumentException::class);
        $schema->validate(array("id" => 1, "username" => "testuser"));
    }

    public function test_invalid_fields_as_array()
    {
        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();
        $this->assertFalse($schema->validate(array('id' => 'wrong!', 'username' => 7)));
    }

    public function test_valid_fields_as_array()
    {
        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();
        $this->assertTrue($schema->validate(array('id' => 7, 'username' => 'wrong!')));
    }

    public function test_invalid_fields_as_model()
    {
        $model = new TestUserModel();
        $model->id = 'testuser';
        $model->username = 7;

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();
        $this->assertFalse($schema->validate($model));
    }

    public function test_valid_fields_as_model()
    {
        $model = new TestUserModel();
        $model->id = 7;
        $model->username = 'testuser';

        $schemaClass = new class() extends Schema {
            private static string $model = TestUserModel::class;

            const id = array('type' => 'integer', 'required' => true, 'nullable' => false);
            const username = array('type' => 'string', 'required' => true, 'nullable' => false);
        };

        $schema = new $schemaClass();
        $this->assertTrue($schema->validate($model));
    }
}
