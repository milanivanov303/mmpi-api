<?php

use App\Models\User;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;

abstract class RestTestCase extends TestCase
{
    use DatabaseTransactions;

    /**
     * Resource primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var FakerGenerator
     */
    protected $faker;

    /**
     * @inheritDoc
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->actingAs(User::first());

        $this->faker = FakerFactory::create();
    }

    /**
     * Get request data
     *
     * @return array
     */
    abstract protected function getData();

    /**
     * Get request invalid data
     *
     * @param array $data
     * @return array
     */
    abstract protected function getInvalidData(array $data);

    /**
     * Get request update data
     *
     * @param array $data
     * @return array
     */
    abstract protected function getUpdateData(array $data);

    /**
     * Get faker instance
     *
     * @return FakerGenerator
     */
    protected function faker()
    {
        return $this->faker;
    }

    /**
     * Get primary key value
     *
     * @param array $data
     * @return mixed
     */
    protected function getPrimaryKeyValue($data)
    {
        return $data[$this->primaryKey] ?? null;
    }

    /**
     * Get changed properties
     *
     * @param array $validData
     * @param array $invalidData
     * @return array
     */
    protected function getChangedProperties($validData, $invalidData)
    {
        // Flatten data and filter empty elements
        // Empty arrays are not flatten for some reason and array_diff will fail for non string elements!
        $validData = array_filter(array_dot($validData));
        $invalidData = array_filter(array_dot($invalidData));

        $diff = array_diff($validData, $invalidData);

        $properties = [];
        foreach ($diff as $key => $value) {
          array_set($properties, $key, $value);
        }

        return array_keys($properties);
    }

    /**
     * Get response data
     *
     * @param TestResponse $response
     * @return array
     */
    protected function getResponseData(TestResponse $response)
    {
        return json_decode($response->getContent(), JSON_OBJECT_AS_ARRAY)['data'];
    }

    /**
     * Get response data
     *
     * @return array
     */
    protected function create($data)
    {
        $this
            ->json('POST', $this->uri, $data)
            ->assertResponseStatus(201);

        return $this->getResponseData($this->response);
    }

    /**
     * Get with
     *
     * @param array $data
     * @return array
     */
    protected function getWith(array $data = [])
    {
        return array_keys(
            array_filter($data, function ($value) {
                return is_array($value);
            })
        );
    }

    /**
     * Test creation
     *
     * @return void
     */
    public function testCreate()
    {
        $data = $this->getData();
        $data = $this->create($data);

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $this->getPrimaryKeyValue($data)
        ]);
    }

    /**
     * Test creation with wrong data
     *
     * @return void
     */
    public function testCreateWithInvalidData()
    {
        $data        = $this->getData();
        $invalidData = $this->getInvalidData($data);
        $changedProperties = $this->getChangedProperties($data, $invalidData);

        foreach ($changedProperties as $changedProperty) {
            $createData = $data;

            if (array_key_exists($changedProperty, $invalidData)) {
                $createData[$changedProperty] = $invalidData[$changedProperty];
            } else {
                unset($createData[$changedProperty]);
            }

            $this
                ->json('POST', $this->uri, $createData)
                ->seeJsonStructure([$changedProperty])
                ->assertResponseStatus(422);
        }

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $this->getPrimaryKeyValue($data)
        ]);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $data       = $this->create($this->getData());
        $updateData = $this->getUpdateData($data);

        $this
            ->json('PUT', $this->uri . '/' . $this->getPrimaryKeyValue($data), $updateData)
            ->seeJson($updateData)
            ->assertResponseOk();

        $this->seeInDatabase($this->table, [
            $this->primaryKey => $this->getPrimaryKeyValue($data)
        ]);
    }

    /**
     * Test delete single
     *
     * @return void
     */
    public function testDelete()
    {
        $data = $this->create($this->getData());

        $this
            ->json('DELETE', $this->uri . '/' . $this->getPrimaryKeyValue($data))
            ->assertResponseStatus(204);

        $this->missingFromDatabase($this->table, [
            $this->primaryKey => $this->getPrimaryKeyValue($data)
        ]);
    }

    /**
     * Test get single
     *
     * @return void
     */
    public function testGet()
    {
        $data = $this->create($this->getData());

        $this
            ->json('GET', $this->uri . '/' . $this->getPrimaryKeyValue($data), [
                'with' => $this->getWith($data)
            ])
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
    public function testGetPaginatedList()
    {
        $this
            ->json('GET', $this->uri . '?page=1')
            ->shouldReturnJson()
            ->seeJsonStructure(['meta' => ['total', 'current_page'], 'data'])
            ->assertResponseOk();
    }
}
