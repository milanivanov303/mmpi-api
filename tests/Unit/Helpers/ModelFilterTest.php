<?php

use App\Helpers\ModelFilter;
use App\Models\Model;
use App\Helpers\DataMapper;

class ModelFilterTest extends TestCase
{
    protected $model;
    protected $modelFilter;

    public function setUp()
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class)->makePartial();

        // Model constructor is not called by mockery! This is why I set mapper here
        $this->model->mapper = new DataMapper([]);

        $this->model->shouldReceive('getColumns')
            ->once()
            ->andReturn(['name', 'email']);

        $this->modelFilter = new ModelFilter($this->model);
    }

    public function test_returns_builder()
    {
        $builder = $this->modelFilter->getBuilder([]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
        $this->assertEquals($this->model, $builder->getModel());
    }

    public function test_adds_limit_and_order_by()
    {
        $builder = $this->modelFilter->getBuilder([
            'limit'     => 1,
            'order_by'  => 'name',
            'order_dir' => 'asc',
        ]);

        $this->assertEquals(1,      $builder->getQuery()->limit);
        $this->assertEquals('name', $builder->getQuery()->orders[0]['column']);
        $this->assertEquals('asc',  $builder->getQuery()->orders[0]['direction']);
    }

    public function test_adds_correct_wheres()
    {
        $faker = Faker\Factory::create();

        $name  = $faker->name;
        $email = $faker->email;

        $builder = $this->modelFilter->getBuilder([
            'name'  => $name,
            'email' => '!= ' . $email
        ]);

        $this->assertNotEmpty($builder->getQuery()->wheres);
        $this->assertArraySubset(['column' => 'name'],  $builder->getQuery()->wheres[0]);
        $this->assertArraySubset(['column' => 'email'], $builder->getQuery()->wheres[1]);

        $this->assertEquals($name,  $builder->getQuery()->wheres[0]['value']);
        $this->assertEquals($email, $builder->getQuery()->wheres[1]['value']);

        $this->assertEquals('=', $builder->getQuery()->wheres[0]['operator']);
        $this->assertEquals('!=', $builder->getQuery()->wheres[1]['operator']);
    }

    public function test_does_not_add_incorrect_wheres()
    {
        $builder = $this->modelFilter->getBuilder([
            'NOT-EXISTING-COLUMN' => 'VALUE'
        ]);

        $this->assertEmpty($builder->getQuery()->wheres);
    }
}