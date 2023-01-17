<?php

namespace GepurIt\OneCClientBundle\Exception;

use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class OneCSyncServerErrorException extends OneCSyncException
{
    /** @var string */
    private $response;

    public function __construct(
        string $message = "",
        int $code = 0,
        ResponseInterface $response = null,
        Throwable $previous = null
    ) {
        if (null !== $response) {

            $this->response = $this->cleanResponseBody($response->getBody()->__toString());
        }

        parent::__construct($message, $code, $previous);
    }

    public static function fromGuzzle(ServerException $exception)
    {
        return new self(
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getResponse(),
            $exception->getPrevious()
        );
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @param string $responseBody
     *
     * @return string
     */
    private function cleanResponseBody(string $responseBody): string
    {
        $responseBody = trim($responseBody);
        if (substr($responseBody, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $responseBody = substr($responseBody, 3);
        }

        return $responseBody;
    }
}
