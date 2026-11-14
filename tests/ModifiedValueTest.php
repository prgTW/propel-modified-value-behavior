<?php
use PHPUnit\Framework\TestCase;
use Propel\Generator\Util\QuickBuilder;

/**
 * Author: Radek
 * Date: 27/10/15 16:57
 */
class ModifiedValueTest extends TestCase
{
	protected function setUp(): void
	{
		// Propel itself still triggers deprecations on recent PHP; only those raised by this package fail the test.
		$packageDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
		set_error_handler(function ($severity, $message, $file, $line) use ($packageDir) {
			if (0 !== strpos($file, $packageDir) || 0 === strpos($file, $packageDir . 'vendor' . DIRECTORY_SEPARATOR))
			{
				return false;
			}
			throw new \ErrorException($message, 0, $severity, $file, $line);
		});

		if (!class_exists('\TestModel'))
		{
			$schema = <<<EOF
<database name="modified_value_test">
    <table name="test_model">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
        <column name="title" type="VARCHAR" size="100" primaryString="true" />
        <column name="age" type="INTEGER" />
        <behavior name="Radekb\ModifiedValueBehavior\ModifiedValueBehavior">
        </behavior>
    </table>
</database>
EOF;
			$builder = new QuickBuilder();
			$builder->setSchema($schema);
			$builder->setVfs(false);
			$builder->build();
		}
	}

	protected function tearDown(): void
	{
		restore_error_handler();
	}

	public function testAddedMethods()
	{
		$object = new \TestModel();
		$this->assertTrue(method_exists($object, 'hasPreviousValueOfTitle'));
		$this->assertTrue(method_exists($object, 'getPreviousValueOfTitle'));
	}

	public function testOperation()
	{
		$object = new \TestModel();
		$object->setTitle('Abc');
		$object->save();

		$object->setTitle('Cdef');

		$this->assertEquals('Cdef', $object->getTitle());
		$this->assertEquals('Abc', $object->getPreviousValueOfTitle());
		$this->assertEquals(true, $object->hasPreviousValueOfTitle());
	}

	public function testDoubleChange()
	{
		$object = new \TestModel();
		$object->setTitle('Abc');
		$object->save();

		$object->setTitle('Cdef');
		$object->setTitle('Ghij');

		$this->assertEquals('Ghij', $object->getTitle());
		$this->assertEquals('Abc', $object->getPreviousValueOfTitle());
	}

	public function testClearingModified()
	{
		$object = new \TestModel();
		$object->setTitle('Abc');
		$object->save();

		$object->setTitle('Cdef');
		$object->save();


		$this->assertEquals(null, $object->getPreviousValueOfTitle());
		$this->assertEquals(false, $object->hasPreviousValueOfTitle());
	}

	public function testAllPrevious()
	{
		$object = new \TestModel();
		$object->setTitle('Abc');
		$object->setAge(10);
		$object->save();

		$object->setTitle('Cdef');
		$object->setAge(20);

		$previous = $object->getAllPreviousValues();

		$this->assertEquals('Abc', $previous[Map\TestModelTableMap::COL_TITLE]);
		$this->assertEquals(10, $previous[Map\TestModelTableMap::COL_AGE]);
	}

	public function testResetModifiedAll()
	{
		$object = new \TestModel();
		$object->setTitle('Abc');
		$object->setAge(10);
		$object->save();

		$object->setTitle('Cdef');
		$object->setAge(20);

		$object->resetModified();

		$previous = $object->getAllPreviousValues();

		$this->assertEquals(null, $object->getPreviousValueOfTitle());
		$this->assertEquals(null, $object->getPreviousValueOfAge());
		$this->assertEquals([], $previous);
	}
}