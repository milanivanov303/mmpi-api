<?php

use \App\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class DeliveryChainsTest extends TestCase
{
    use DatabaseTransactions;

    protected $uri        = 'api/v1/delivery-chains';
    protected $table      = 'delivery_chains';
    protected $primaryKey = 'title';

    public function setUp() {
        parent::setUp();
        $this->actingAs(User::first());
    }
    
    /**
     * Get request data
     *
     * @return array
     */
    public function getData()
    {
        $faker = Faker\Factory::create();

        $dlvryType = \App\Models\EnumValue::where('type', 'dc_dlvry_type')->inRandomOrder()->minimal()->first();
        $status    = \App\Models\EnumValue::where('type', 'active_inactive')->inRandomOrder()->minimal()->first();
        $dcVersion = \App\Models\EnumValue::where('type', 'delivery_chain_version')->inRandomOrder()->minimal()->first();
        $dcRole    = \App\Models\EnumValue::where('type', 'delivery_chain_role')->inRandomOrder()->minimal()->first();
        $type      = \App\Modules\DeliveryChains\Models\DeliveryChainType::inRandomOrder()->first();

        return [
            'title'                => $faker->word(),
            'patch_directory_name' => $faker->word(),
            'dlvry_type'           => $dlvryType->toArray(),
            'status'               => $status->toArray(),
            'dc_version'           => $dcVersion->toArray(),
            'dc_role'              => $dcRole->toArray(),
            'type'                 => $type->toArray()
        ];
    }

    /**
     * Test creation
     *
     * @return void
     */
    public function testCreate()
    {
        $data = $this->getData();

        $this
            ->json('POST', $this->uri, $data)
            ->seeJson($data)
            ->assertResponseStatus(201);

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]
        ]);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $data = $this->getData();

        // Set invalid parameters
        $data['patch_directory_name'] = 123;

        // remove required parameters
        unset($data['type']);

        $this
            ->json('POST', $this->uri, $data)
            ->seeJsonStructure(['patch_directory_name', 'type'])
            ->assertResponseStatus(422);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]
        ]);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->get( $this->uri . '/' . $data[$this->primaryKey])
            ->seeJson($data)
            ->assertResponseOk();
    }

    /**
     * Test get non existing
     *
     * @return void
     */
    public function testGetNonExisting()
    {
        $this
            ->get($this->uri . '/NON-EXISTING')
            ->assertResponseStatus(404);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        // Change parameters
        $data['patch_directory_name'] = 'UPDATED_PATH';

        $this
            ->json('PUT', $this->uri . '/' . $data[$this->primaryKey], $data)
            ->seeJson($data)
            ->assertResponseOk();

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]
        ]);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        $data = $this->getData();

        $this->json('POST', $this->uri, $data);

        $this
            ->json('DELETE', $this->uri . '/' . $data[$this->primaryKey])
            ->assertResponseStatus(204);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $data[$this->primaryKey]]
        );
    }

    /**
     * Test get list
     *
     * @return void
     */
    public function testGetList()
    {
        $this
            ->json('GET', $this->uri . '?limit=10')
            ->shouldReturnJson()
            ->seeJsonStructure(['data'])
            ->assertResponseOk();
    }


    /**
     * Test get paginated list
     *
     * @return void
     */
    public function testGetPaginatedIssuesList()
    {
        $this
            ->json('GET', $this->uri . '?page=3')
            ->seeJsonStructure(['meta' => ['total', 'current_page'], 'data'])
            ->assertResponseOk();
    }
}
