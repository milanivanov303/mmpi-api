<?php

use App\Models\Model;

class ModelTest extends TestCase
{
    protected $model;

    public function setUp()
    {
        parent::setUp();
        $this->model = new Model();
    }

    function test_is_visible()
    {
        $this->model->setVisible(['name', 'email']);

        $this->assertTrue($this->model->isVisible('name'));
        $this->assertFalse($this->model->isVisible('username'));
    }

    function test_saves_visible_one_new_instance()
    {
        $this->model->setVisible(['name', 'email']);

        $model = $this->model->newInstance();

        $this->assertEquals($this->model->getVisible(), $model->getVisible());
    }
}
