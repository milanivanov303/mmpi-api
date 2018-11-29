<?php

use Core\Helpers\JsonSchema\Filters\CheckInDbFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Core\Models\Model;

class CheckInDbFilterTest extends TestCase
{
    /**
     * @var CheckInDbFilter
     */
    protected $filter;

    public function setUp()
    {
        parent::setUp();

        $this->filter = new CheckInDbFilter();

        // Mock Request
        $this->app->bind(\Illuminate\Http\Request::class, function () {
            $mock = \Mockery::mock(\Illuminate\Http\Request::class)->makePartial();
            $mock->shouldReceive('method')->once()->andReturn('PUT');
            return $mock;
        });
    }

    public function test_calls_validator()
    {
        $faker = Faker\Factory::create();

        $name = $faker->userName;
        $rule = 'exists:users,username';

        // Mock Validator
        Validator::shouldReceive('make')
                ->once()
                ->with(['field' => $name], ['field' => $rule])
                ->andReturn(Mockery::mock(['passes' => true]));

        $this->filter->validate($name, ['rule' => $rule]);
    }

    public function test_does_not_calls_validator_fot_nulls()
    {
        $name = null;
        $rule = 'exists:users,username';

        // Mock Validator
        Validator::shouldReceive('make')->never();

        $result = $this->filter->validate($name, ['rule' => $rule]);

        $this->assertTrue($result);
    }

    public function test_adds_id_on_update_unique()
    {
        $faker = Faker\Factory::create();

        $name = $faker->userName;
        $rule = 'unique:users,username';

        // create new model and manually set its id
        $model = new Model();
        $model->id = 12;

        // Mock DB
        DB::shouldReceive('table')
            ->once()
            ->andReturn(Mockery::mock(['where' => Mockery::mock(['first' => $model])]));

        // Mock Validator
        Validator::shouldReceive('make')
            ->once()
            ->with(['field' => $name], ['field' =>$rule . ',' . $model->id])
            ->andReturn(Mockery::mock(['passes' => true]));

        $this->filter->validate($name, ['rule' => $rule]);
    }

    public function test_does_not_adds_id_on_update_unique_when_no_model_found()
    {
        $faker = Faker\Factory::create();

        $name = $faker->userName;
        $rule = 'unique:users,username';

        // Mock DB
        DB::shouldReceive('table')
            ->once()
            ->andReturn(Mockery::mock(['where' => Mockery::mock(['first' => false])]));

        // Mock Validator
        Validator::shouldReceive('make')
            ->once()
            ->with(['field' => $name], ['field' =>$rule])
            ->andReturn(Mockery::mock(['passes' => true]));

        $this->filter->validate($name, ['rule' => $rule]);
    }
}