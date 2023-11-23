<?php

namespace App\Helpers\Gitlab;

class Response extends \Illuminate\Http\Response
{
    public function getData() : ?array
    {
        return json_decode((string) $this->getContent(), JSON_OBJECT_AS_ARRAY);
    }

    public function isSuccessful() : bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    public function isUnsuccessful() : bool
    {
        return !$this->isSuccessful();
    }

    public function isUnauthorized() : bool
    {
        return $this->getStatusCode() === 401;
    }
}
