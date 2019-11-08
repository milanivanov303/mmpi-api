<?php
use Modules\Sources\Models\Source;
use App\Models\Department;

class SourcesTest extends RestTestCase
{
    protected $uri        = 'v1/sources';
    protected $table      = 'source';
    protected $primaryKey = 'source_id';

    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        $department  = Department::inRandomOrder()->first();

        return [
            'source_name'             => $this->faker()->name(100),
            'source_path'             => $this->faker()->text(200),
            'source_status'           => $this->faker()->numberBetween(0, 2),
            'comment'                 => $this->faker()->text(50),
            'department'              => $department->toArray(),
            'dependencies'            => $this->faker()->numberBetween(0,1),
            'library'                 => $this->faker()->numberBetween(0,1)
        ];
    }

   /**
     * Get request invalid data
     *
     * @param array $data
     * @return array
     */
    protected function getInvalidData(array $data)
    {
        // Set invalid parameters
        $data['source_status'] = $this->faker()->text(200);

        return $data;
    }

    /**
     * Get request update data
     *
     * @param array $data
     * @return array
     */
    protected function getUpdateData(array $data)
    {
        // Change parameters
        $data['comment'] = 'UPDATED_COMMENT';

        //Remove date as it is overwritten on each request
        unset($data['department_assigned_on']);

        return $data;
    }

     /**
     * Test creation
     *
     * @return void
     */
    public function testCreate()
    {
        // not yet implemented
        $this
            ->json('POST', $this->uri)
            ->assertResponseStatus(405);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        // not yet implemented
        $this
            ->json('POST', $this->uri)
            ->assertResponseStatus(405);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        // not yet implemented
        $this
            ->json('PUT', $this->uri)
            ->assertResponseStatus(405);
    }

    /**
     * Test delete single
     *
     * @return void
     */
    public function testDelete()
    {
         // not yet implemented
         $this
            ->json('DELETE', $this->uri)
            ->assertResponseStatus(405);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {   
        // not yet implemented
        $data = Source::inRandomOrder()->first()->toArray();

        $this
            ->json('GET', $this->uri . '/' . $this->getPrimaryKeyValue($data), [
                'with' => $this->getWith($data)
            ])
            ->seeJson($data)
            ->assertResponseOk();
    }
}
