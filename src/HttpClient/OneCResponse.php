<?php

namespace GepurIt\OneCClientBundle\HttpClient;

use Psr\Http\Message\ResponseInterface;

class OneCResponse
{
    /** @var int $status */
    private $status;

    /** @var string $data */
    private $data;

    /** @var \string[][] $headers */
    private $headers;

    /**
     * @param ResponseInterface $httpResponse
     */
    public function __construct (ResponseInterface $httpResponse)
    {
        $this->headers = $httpResponse->getHeaders();
        $this->status = $httpResponse->getStatusCode();
        $this->data = $this->normalize($httpResponse->getBody()->getContents());
    }

    /**
     * @return int
     */
    public function getStatus (): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getData (): string
    {
        return $this->data;
    }

    /**
     * @return \string[][]
     */
    public function getHeader(): array
    {
        return $this->headers;
    }

    /**
     * Removes BOM from string
     * @param string $str
     * @return string $str
     */
    private function normalize(string $str = ""): string
    {
        if (substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $str = substr($str, 3);
        }

        return $str;
    }
}
