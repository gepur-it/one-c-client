<?php
declare(strict_types=1);

namespace GepurIt\OneCClientBundle\HttpClient;

use Psr\Http\Message\ResponseInterface;

class OneCResponse
{
    private int $status;
    private string $data;
    /** @var string[][] $headers */
    private array $headers = [];

    /**
     * @param ResponseInterface $httpResponse
     */
    public function __construct(ResponseInterface $httpResponse)
    {
        $this->headers = $httpResponse->getHeaders() ?? [];
        $this->status = $httpResponse->getStatusCode() ?? 0;
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
