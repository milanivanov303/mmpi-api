<?php

use App\Helpers\ModelFilter;
use App\Models\Model;

class ModelFilterTest extends TestCase
{
    protected $model;
    protected $modelFilter;

    public function setUp()
    {
        parent::setUp();

        $this->model = new Model;

        $this->modelFilter = $this->getMockBuilder(ModelFilter::class)
                                  ->setConstructorArgs([new Model])
                                  ->setMethods(['getColumns'])
                                  ->getMock();

        $this->modelFilter->expects($this->any())
                          ->method('getColumns')
                          ->willReturn(['name', 'email']);
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
            'limit' => 1,
            'order_by' => 'name',
            'order_dir' => 'asc',
        ]);

        $this->assertEquals(1, $builder->getQuery()->limit);
        $this->assertEquals('name', $builder->getQuery()->orders[0]['column']);
        $this->assertEquals('asc', $builder->getQuery()->orders[0]['direction']);
    }

    public function test_adds_correct_wheres()
    {
        $builder = $this->modelFilter->getBuilder([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ]);

        $this->assertArraySubset(['column' => 'name'], $builder->getQuery()->wheres[0]);
        $this->assertArraySubset(['column' => 'email'], $builder->getQuery()->wheres[1]);

        $this->assertEquals('John Doe', $builder->getQuery()->wheres[0]['value']);
        $this->assertEquals('john.doe@example.com', $builder->getQuery()->wheres[1]['value']);

        $this->assertEquals('=', $builder->getQuery()->wheres[0]['operator']);
        $this->assertEquals('=', $builder->getQuery()->wheres[1]['operator']);
    }

    public function test_does_not_add_incorrect_wheres()
    {
        $builder = $this->modelFilter->getBuilder([
            'username' => 'jdoe'
        ]);

        $this->assertEmpty($builder->getQuery()->wheres);
    }
}