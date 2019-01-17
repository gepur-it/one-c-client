<?php

namespace GepurIt\OneCClientBundle\HttpClient;

use GepurIt\OneCClientBundle\Event\RequestSentEvent;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GepurIt\OneCClientBundle\Exception\OneCSyncClientErrorException;
use GepurIt\OneCClientBundle\Exception\OneCSyncException;
use GepurIt\OneCClientBundle\Exception\OneCSyncServerErrorException;
use GepurIt\OneCClientBundle\Request\OneCRequest;
use GepurIt\OneCClientBundle\Rabbit\RequestQueue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ApiHttpClient
 * @package GepurIt\OneCClientBundle\HttpClient
 */
class ApiHttpClient
{
    /**
     *
     * Constants contains list of available response codes from 1c API
     *
     * HTTP response codes info links
     *      https://developer.mozilla.org/ru/docs/Web/HTTP/Status
     *      https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     */

    /**
     * OK, Success, etc.
     */
    const SUCCESS__CODE   = 200;
    const RESPONSE_REGEXP = '';
    /**
     * Unauthorized
     * Authentication is needed to get requested response
     *
     * In case of response to 1c it means wrong hash
     */
    const UNAUTHORIZED__CODE = 401;
    /**
     * Not Found
     * Server can not find requested resource.
     */
    const NOT_FOUND__CODE = 404;

    /**
     * Unprocessable Entity
     * he request was well-formed but was unable to be followed due to semantic errors.
     */
    const ENTITY_PROCESSING_ERROR__CODE = 422;

    /** @var ClientInterface $client */
    private $client;

    /** @var string $resource */
    private $resource;

    /** @var string $token */
    private $token;

    /** @var RequestQueue  */
    private $queue;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ApiHttpClient constructor.
     *
     * @param ClientInterface          $client
     * @param string                   $resource
     * @param string                   $token
     * @param RequestQueue             $queue
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ClientInterface $client,
        string $resource,
        string $token,
        RequestQueue $queue,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->client        = $client;
        $this->token         = $token;
        $this->queue = $queue;

        $suffix         = '/';
        $resource       = rtrim($resource, $suffix).$suffix;
        $this->resource = $resource;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $request
     *
     * @return string
     */
    public function generateGetQuery(string $request): string
    {
        $hash = $this->generateHash($request);
        $uri  = $this->resource.$request.'/'.$hash;

        return $uri;
    }

    /**
     * @param string $request
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     */
    public function requestGet(string $request): OneCResponse
    {
        $hash = $this->generateHash($request);
        $uri  = $this->resource.$request.'/'.$hash;

        return $this->request('GET', $uri, []);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function generateHash(string $content): string
    {
        return md5($content.$this->token);
    }

    /**
     * @param OneCRequest $request
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     * @throws \Exception
     */
    public function sendRequest(OneCRequest $request): OneCResponse
    {
        switch ($request->getMethod()) {
            case OneCRequest::METHOD__POST:
                $this->eventDispatcher->dispatch(RequestSentEvent::NAME, new RequestSentEvent($request));
                $result = $this->requestPost($request->getRoute(), $request->getData());
                break;
            case OneCRequest::METHOD__GET:
                $result = $this->requestGet($request->getRoute());
                break;
            default:
                throw new OneCSyncException("Unknown method {$request->getMethod()}");
        }

        return $result;
    }

    /**
     * @param OneCRequest $request
     *
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function queueRequest(OneCRequest $request)
    {
        $message = json_encode($request);
        $this->queue->publish($message);
    }

    /**
     * @param string $request
     * @param array  $requestData
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     */
    private function requestPost(string $request, array $requestData): OneCResponse
    {
        $body = json_encode($requestData);
        $hash = $this->generateHash($request.$body);
        $uri  = $this->resource.$request.'/'.$hash;

        return $this->request('POST', $uri, ['body' => $body]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $requestData
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     */
    private function request(string $method, string $uri, array $requestData)
    {
        try {
            $response = $this->client->request($method, $uri, $requestData);
        } catch (ServerException $exception) {
            throw OneCSyncServerErrorException::fromGuzzle($exception);
        } catch (ClientException $exception) {
            throw OneCSyncClientErrorException::fromGuzzle($exception);
        } catch (RequestException $exception) {
            throw new OneCSyncException($exception->getMessage(), $exception->getCode());
        } catch (GuzzleException $exception) {
            throw new OneCSyncException($exception->getMessage(), $exception->getCode());
        }

        return new OneCResponse($response);
    }
}
