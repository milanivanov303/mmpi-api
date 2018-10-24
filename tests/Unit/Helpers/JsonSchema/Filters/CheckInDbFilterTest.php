<?php

use App\Helpers\JsonSchema\Filters\CheckInDbFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Model;

class CheckInDbFilterTest extends TestCase
{
    protected $filter;

    public function setUp()
    {
        parent::setUp();

        $this->filter = new CheckInDbFilter();
    }

    public function test_calls_validator()
    {
        $name = 'jdoe';
        $rule = 'exists:users,username';

        // Mock Validator
        Validator::shouldReceive('make')
                ->once()
                ->with(['field' => $name], ['field' => $rule])
                ->andReturn(Mockery::mock(['passes' => true]));

        $this->filter->validate($name, ['rule' => $rule]);
    }

    public function test_adds_id_on_update_unique()
    {
        $name = 'jdoe';
        $rule = 'unique:users,username';

        // create new model and manually set its id
        $model = new Model();
        $model->id = 12;

        // Mock Request
        app()->bind(\Illuminate\Http\Request::class, function () {
            $mock = \Mockery::mock(\Illuminate\Http\Request::class)->makePartial();
            $mock->shouldReceive('method')->once()->andReturn('PUT');
            return $mock;
        });

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
}