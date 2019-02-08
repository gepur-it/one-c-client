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

    /** @var array  */
    private $errors = [];

    public function __construct(
        string $message = "",
        int $code = 0,
        ResponseInterface $response = null,
        Throwable $previous = null
    ) {
        $message = preg_replace('/\s+/', ' ', trim($message));
        parent::__construct($message, $code, $previous);

        if (null !== $response) {
            $responseText = $this->cleanResponseBody($response->getBody()->__toString());
        } else {
            $responseText = '';
        }

        if ($code === 400) {
            $this->parseResponse($responseText);
        }
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
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function parseResponse(?string $responseText)
    {
        $body = json_decode($responseText, true);
        if (null === $body || empty($body['status'])) {
            return;
        }

        switch ($body['status']) {
            case "invalid version":
                $this->code = 412;
                $this->message = "Invalid version";
                break;
            case "order blocked":
                $this->code = 409;
                $this->message = "Order locked on OneC";
                break;
            default:
                $this->errors = $body;
        }
    }

    /**
     * @return string
     */
    public function getResponse()
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
