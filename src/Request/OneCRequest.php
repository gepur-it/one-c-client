<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since : 23.11.18
 */

namespace  GepurIt\OneCClientBundle\Request;

/**
 * Class OneCRequest
 * @package OneCBundle\Request
  */
class OneCRequest implements \JsonSerializable
{
    const METHOD__GET = 'GET';
    const METHOD__POST = 'POST';

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $data;

    /**
     * OneCRequest constructor.
     *
     * @param string $rote
     * @param string $method
     * @param array  $data
     */
    public function __construct(string $rote, string $method = self::METHOD__GET, array $data = [])
    {
        $this->route = $rote;
        $this->method = $method;
        $this->data = $data;
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
        ];
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
