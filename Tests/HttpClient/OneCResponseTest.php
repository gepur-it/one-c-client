<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 10.11.17
 */
declare(strict_types=1);

namespace GepurIt\OneCClientBundle\Tests\HttpClient;

use GepurIt\OneCClientBundle\HttpClient\OneCResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class OneCResponseTest
 * @package GepurIt\OneCClientBundle\Tests\HttpClient
 */
class OneCResponseTest extends TestCase
{
    public function testGetterMethods()
    {
        $streamMock = $this->getStreamInterfaceMock();
        $streamMock->expects($this->once())
            ->method('getContents')
            ->willReturn('string');

        $responseMock = $this->getResponseInterfaceMock();
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(2);
        $responseMock->expects($this->once())
            ->method('getHeaders')
            ->willReturn([]);

        $oneCResponse = new OneCResponse($responseMock);

        $this->assertEquals(2, $oneCResponse->getStatus());
        $this->assertEquals('string', $oneCResponse->getData());
        $this->assertEquals([], $oneCResponse->getHeader());
    }

    /**
     * @return MockObject|ResponseInterface
     */
    private function getResponseInterfaceMock()
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
