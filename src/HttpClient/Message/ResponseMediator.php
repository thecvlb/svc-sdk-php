<?php

namespace CVLB\Svc\Api\HttpClient\Message;

use Psr\Http\Message\ResponseInterface;

class ResponseMediator
{
    /**
     * @param ResponseInterface $response
     * @return null|array<mixed>
     */
    public static function getContent(ResponseInterface $response): null|array
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
