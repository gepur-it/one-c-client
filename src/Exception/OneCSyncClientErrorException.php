<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 19.10.17
 */

namespace GepurIt\OneCClientBundle\Exception;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class OneCSyncClientErrorException
 * @package OneCBundle\Exception
 */
class OneCSyncClientErrorException extends OneCSyncException
{
    /** @var string */
    private $response = '';

    public function __construct(
        string $message = "",
        int $code = 0,
        ResponseInterface $response = null,
        Throwable $previous = null
    ) {
        if (null !== $response) {
            $this->response = trim($response->getBody()->__toString());
        }

        parent::__construct($message, $code, $previous);
    }

    public static function fromGuzzle(ClientException $exception)
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
    public function getResponse()
    {
        return $this->response;
    }
}

