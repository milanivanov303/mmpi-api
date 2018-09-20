<?php

class UsersTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testGetUsersList()
    {
        //$user = factory('App\Models\User')->create(); /// creates new user in db!
        $user = \App\Models\User::find(1);

        $response = $this->actingAs($user)->call('GET', '/api/v1/users');

        $this->assertEquals(200, $response->status());

        //var_dump($response->getData(JSON_OBJECT_AS_ARRAY));

    }
}
