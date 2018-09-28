<?php

namespace App\Exceptions;

use Exception;

class QueryResponseException extends Exception
{
    /**
     * The recommended response to send to the client.
     *
     * @var \Exception
     */
    public $previous;

    /**
     * The status code to use for the response.
     *
     * @var int
     */
    public $status = 400;

    /**
     * Create a new HTTP response exception instance.
     *
     * @param  \Exception  $previous
     * @return void
     */
    public function __construct(\Exception $previous = null)
    {
        $this->previous = $previous;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse()
    {
        return response('There was an error', $this->status);
    }
}
