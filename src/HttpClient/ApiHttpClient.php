<?php

namespace GepurIt\OneCClientBundle\HttpClient;

use GepurIt\OneCClientBundle\Event\RequestSentErrorEvent;
use GepurIt\OneCClientBundle\Event\RequestSentSuccessEvent;
use GepurIt\OneCClientBundle\Exception\OneCSyncClientErrorException;
use GepurIt\OneCClientBundle\Exception\OneCSyncException;
use GepurIt\OneCClientBundle\Exception\OneCSyncServerErrorException;
use GepurIt\OneCClientBundle\OneCClientInterface;
use GepurIt\OneCClientBundle\Rabbit\RequestDeferredQueue;
use GepurIt\OneCClientBundle\Rabbit\RequestQueue;
use GepurIt\OneCClientBundle\Request\OneCRequest;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ApiHttpClient
 * @package GepurIt\OneCClientBundle\HttpClient
 */
class ApiHttpClient implements OneCClientInterface
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
    const SUCCESS__CODE = 200;
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

    private ClientInterface $client;
    private string $resource = '';
    private string $login = '';
    private string $password = '';
    private bool $auth = false;
    private RequestQueue $queue;
    private EventDispatcherInterface $eventDispatcher;
    private RequestDeferredQueue $deferredQueue;

    public function __construct(
        ClientInterface $client,
        string $resource,
        string $login,
        string $password,
        bool $auth,
        RequestQueue $queue,
        RequestDeferredQueue $deferredQueue,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->client = $client;
        $this->queue = $queue;

        $suffix = '/';
        $resource = rtrim($resource, $suffix).$suffix;
        $this->resource = $resource;
        $this->login = $login;
        $this->password = $password;
        $this->auth = $auth;
        $this->eventDispatcher = $eventDispatcher;
        $this->deferredQueue = $deferredQueue;
    }

    public function generateGetQuery(string $request): string
    {
        return $this->resource.$request;
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
        $uri = $this->resource.$request;
        return $this->request('GET', $uri, []);
    }

    /**
     * @param OneCRequest $request
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     */
    public function sendRequest(OneCRequest $request): OneCResponse
    {
        switch ($request->getMethod()) {
            case OneCRequest::METHOD__POST:
                try {
                    $result = $this->requestPost($request->getRoute(), $request->getData());
                    $this->eventDispatcher->dispatch(new RequestSentSuccessEvent($request, $result));
                } catch (OneCSyncException $exception) {
                    $this->eventDispatcher->dispatch(new RequestSentErrorEvent($request, $exception));
                    throw $exception;
                }

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
     */
    public function queueRequest(OneCRequest $request)
    {
        $message = json_encode($request);
        $this->queue->push($message);
    }

    /**
     * @param OneCRequest $request
     */
    public function queueRequestProrogued(OneCRequest $request)
    {
        $message = json_encode($request);
        $this->deferredQueue->push($message);
    }

    /**
     * @param string $request
     * @param array $requestData
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     */
    public function requestPost(string $request, array $requestData): OneCResponse
    {
        $body = json_encode($requestData);
        $uri = $this->resource.$request;

        return $this->request('POST', $uri, ['body' => $body]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $requestData
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     */
    public function request(string $method, string $uri, array $requestData)
    {
        $uri = new Uri($uri);
        if ($this->auth) {
            $requestData = array_merge(
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => "Basic ".base64_encode("{$this->login}:{$this->password}")
                    ]
                ],
                $requestData
            );
        }
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
