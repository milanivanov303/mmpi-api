<?php

use Core\Helpers\ModelFilter;
use Core\Models\Model;

/**
 * Class TestModel
 * Used to test ModelFilter class
 */
class TestModel extends Model
{
    public function getColumns () : array
    {
        return ['name', 'email'];
    }

    public function filters () : array
    {
        return [
            'department' => function ($query, $value) {
                return $query->where('department', $value);
            }
        ];
    }

    public function orderBy () : array
    {
        return [
            'department' => function ($query, $order_dir) {
                return $query->orderBy('department', $order_dir);
            }
        ];
    }
}

class ModelFilterTest extends TestCase
{
    protected $filter;

    public function setUp()
    {
        parent::setUp();
        $this->filter = new ModelFilter(new TestModel());
    }

    public function test_returns_builder()
    {
        $builder = $this->filter->getBuilder([]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
        $this->assertEquals(new TestModel(), $builder->getModel());
    }

    public function test_adds_limit_and_order_by()
    {
        $builder = $this->filter->getBuilder([
            'limit'     => 1,
            'order_by'  => 'name',
            'order_dir' => 'asc',
        ]);

        $this->assertEquals(1,      $builder->getQuery()->limit);
        $this->assertEquals('name', $builder->getQuery()->orders[0]['column']);
        $this->assertEquals('asc',  $builder->getQuery()->orders[0]['direction']);
    }

    public function test_adds_order_by_from_callback()
    {
        $builder = $this->filter->getBuilder([
            'order_by'  => 'department'
        ]);

        $this->assertEquals('department', $builder->getQuery()->orders[0]['column']);
    }

    public function test_adds_wheres_from_callback()
    {
        $department = 'Enterprise Applications';

        $builder = $this->filter->getBuilder([
            'department'  => $department
        ]);

        $this->assertNotEmpty($builder->getQuery()->wheres);
        $this->assertArraySubset(['column' => 'department'],  $builder->getQuery()->wheres[0]);
        $this->assertEquals($department,  $builder->getQuery()->wheres[0]['value']);
    }

    public function test_adds_correct_wheres()
    {
        $faker = Faker\Factory::create();

        $name  = $faker->name;
        $email = $faker->email;

        $builder = $this->filter->getBuilder([
            'name'  => $name,
            'email' => 'like ' . $email
        ]);

        $this->assertNotEmpty($builder->getQuery()->wheres);
        $this->assertArraySubset(['column' => 'name'],  $builder->getQuery()->wheres[0]);
        $this->assertArraySubset(['column' => 'email'], $builder->getQuery()->wheres[1]);

        $this->assertEquals($name,  $builder->getQuery()->wheres[0]['value']);
        $this->assertEquals($email . '%', $builder->getQuery()->wheres[1]['value']);

        $this->assertEquals('=', $builder->getQuery()->wheres[0]['operator']);
        $this->assertEquals('like', $builder->getQuery()->wheres[1]['operator']);
    }

    public function test_does_not_add_incorrect_wheres()
    {
        $builder = $this->filter->getBuilder([
            'NOT-EXISTING-COLUMN' => 'VALUE'
        ]);

        $this->assertEmpty($builder->getQuery()->wheres);
    }
}