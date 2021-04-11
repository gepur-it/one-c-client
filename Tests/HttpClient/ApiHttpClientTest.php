<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 10.11.17
 */
declare(strict_types=1);

namespace GepurIt\OneCClientBundle\Tests\HttpClient;

use GepurIt\OneCClientBundle\HttpClient\ApiHttpClient;
use GepurIt\OneCClientBundle\HttpClient\OneCResponse;
use GepurIt\OneCClientBundle\Rabbit\RequestDeferredQueue;
use GepurIt\OneCClientBundle\Rabbit\RequestQueue;
use GepurIt\OneCClientBundle\Security\HashGenerator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GepurIt\OneCClientBundle\Exception\OneCSyncClientErrorException;
use GepurIt\OneCClientBundle\Exception\OneCSyncException;
use GepurIt\OneCClientBundle\Exception\OneCSyncServerErrorException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ApiHttpClientTest
 * @package OneCBundle\HttpClient
 */
class ApiHttpClientTest extends TestCase
{
    /**
     * @throws OneCSyncClientErrorException
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     */
    public function testRequestGet()
    {
        $request = 'request';
        $resource = 'resource';

        $streamMock = $this->getStreamInterfaceMock();
        $streamMock->expects($this->once())
            ->method('getContents')
            ->willReturn('string');

        $responseMock = $this->getResponseMock();
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);

        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($responseMock);
        $requestQueue = $this->createMock(RequestQueue::class);
        $requestDefQueue = $this->createMock(RequestDeferredQueue::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $hashGenerator = $this->createMock(HashGenerator::class);
        $apiHttpClient = new ApiHttpClient(
            $clientMock,
            $resource,
            'login',
            'password',
            true,
            $hashGenerator,
            $requestQueue,
            $requestDefQueue,
            $eventDispatcher
        );
        $res = $apiHttpClient->requestGet($request);
        $this->assertInstanceOf(OneCResponse::class, $res);
    }


    /**
     * Test method requestGet()
     * when arg Client() throw ServerException
     */
    public function testThrowServerExceptionInRequestGet()
    {
        $this->expectException(OneCSyncServerErrorException::class);
        $request = 'request';
        $resource = 'resource';
        $message = get_class(new OneCSyncServerErrorException('string_of_message'));
        $requestInterfaceMock = $this->getRequestInterfaceMock();
        $responseExcMock = $this->getResponseMock();
        $exception = new ServerException($message, $requestInterfaceMock, $responseExcMock);

        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willThrowException($exception);

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())
            ->method('__toString')
            ->willReturn('string');

        $responseExcMock->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $requestQueue = $this->createMock(RequestQueue::class);
        $requestDefQueue = $this->createMock(RequestDeferredQueue::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $hashGenerator = $this->createMock(HashGenerator::class);
        $apiHttpClient = new ApiHttpClient(
            $clientMock,
            $resource,
            'login',
            'password',
            true,
            $hashGenerator,
            $requestQueue,
            $requestDefQueue,
            $eventDispatcher
        );
        $res = $apiHttpClient->requestGet($request);
        $this->assertInstanceOf(OneCResponse::class, $res);
    }

    /**
     * Test method requestGet()
     * when arg Client() throw ClientException
     */
    public function testThrowClientExceptionInRequestGet()
    {
        $this->expectException(OneCSyncClientErrorException::class);
        $request = 'request';
        $resource = 'resource';

        $message = get_class(new OneCSyncClientErrorException('string_of_message'));
        $requestInterfaceMock = $this->getRequestInterfaceMock();
        $responseExcMock = $this->getResponseMock();
        $exception = new ClientException($message, $requestInterfaceMock, $responseExcMock);

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->once())
            ->method('__toString')
            ->willReturn('string');

        $responseExcMock->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willThrowException($exception);

        $requestQueue = $this->createMock(RequestQueue::class);
        $requestDefQueue = $this->createMock(RequestDeferredQueue::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $hashGenerator = $this->createMock(HashGenerator::class);
        $apiHttpClient = new ApiHttpClient(
            $clientMock,
            $resource,
            'login',
            'password',
            true,
            $hashGenerator,
            $requestQueue,
            $requestDefQueue,
            $eventDispatcher
        );
        $res = $apiHttpClient->requestGet($request);
        $this->assertInstanceOf(OneCResponse::class, $res);
    }

    /**
     * Test method requestGet()
     * when arg Client() throw RequestException
     */
    public function testThrowRequestExceptionInRequestGet()
    {
        $this->expectException(OneCSyncException::class);
        $request = 'request';
        $resource = 'resource';

        $message = get_class(new OneCSyncException('string_of_message'));
        $requestInterfaceMock = $this->getRequestInterfaceMock();
        $responseExcMock = $this->getResponseMock();
        $exception = new RequestException($message, $requestInterfaceMock, $responseExcMock);

        $clientMock = $this->getClientMock();
        $clientMock->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willThrowException($exception);

        $requestQueue = $this->createMock(RequestQueue::class);
        $requestDefQueue = $this->createMock(RequestDeferredQueue::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $hashGenerator = $this->createMock(HashGenerator::class);
        $apiHttpClient = new ApiHttpClient(
            $clientMock,
            $resource,
            'login',
            'password',
            true,
            $hashGenerator,
            $requestQueue,
            $requestDefQueue,
            $eventDispatcher
        );
        $res = $apiHttpClient->requestGet($request);
        $this->assertInstanceOf(OneCResponse::class, $res);
    }


    /**
     * @return MockObject|RequestInterface
     */
    private function getRequestInterfaceMock()
    {
        return $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            //->setMethods(['request'])
            ->getMock();
    }

    /**
     * @return MockObject|Client
     */
    private function getClientMock()
    {
        return $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['request'])
            ->getMock();
    }

    /**
     * @return MockObject|ResponseInterface
     */
    private function getResponseMock()
    {
        return $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|StreamInterface
     */
    private function getStreamInterfaceMock()
    {
        return $this->getMockBuilder(StreamInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
