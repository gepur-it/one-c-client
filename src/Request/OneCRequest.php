<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 23.11.18
 */
declare(strict_types=1);

namespace  GepurIt\OneCClientBundle\Request;

/**
 * Class OneCRequest
 * @package OneCBundle\Request
  */
class OneCRequest implements \JsonSerializable
{
    const METHOD__GET = 'GET';
    const METHOD__POST = 'POST';

    private string $route;
    private string $method;
    private array $data;
    private array $supportData = [];

    /**
     * OneCRequest constructor.
     *
     * @param string $rote
     * @param string $method
     * @param array  $data
     * @param array  $supportData
     */
    public function __construct(
        string $rote,
        string $method = self::METHOD__GET,
        array $data = [],
        array $supportData = []
    )
    {
        $this->route = $rote;
        $this->method = $method;
        $this->data = $data;
        $this->supportData = $supportData;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'route' => $this->getRoute(),
            'method' => $this->getMethod(),
            'data' => $this->getData(),
            'supportData' => $this->getSupportData(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function getSupportData(): array
    {
        return $this->supportData;
    }

    /**
     * @param array $supportData
     */
    public function setSupportData(array $supportData): void
    {
        $this->supportData = $supportData;
    }
}
