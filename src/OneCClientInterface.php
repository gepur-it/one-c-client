<?php
/**
 * one-c-client.
 * Author: Andrii Yakovlev <yawa20@gmail.com>
 * Date: 22.04.19
 */
declare(strict_types=1);

namespace GepurIt\OneCClientBundle;

use GepurIt\OneCClientBundle\Exception\OneCSyncClientErrorException;
use GepurIt\OneCClientBundle\Exception\OneCSyncException;
use GepurIt\OneCClientBundle\Exception\OneCSyncServerErrorException;
use GepurIt\OneCClientBundle\HttpClient\OneCResponse;
use GepurIt\OneCClientBundle\Request\OneCRequest;

interface OneCClientInterface
{
    /**
     * @param string $request
     * @return string
     */
    public function generateGetQuery(string $request): string;

    /**
     * @param string $request
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     * @internal use sendRequest() instead
     */
    public function requestGet(string $request): OneCResponse;

    /**
     * @param string $request
     * @param array $requestData
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     * @internal use sendRequest() instead
     */
    public function requestPost(string $request, array $requestData): OneCResponse;

    /**
     * @param string $method
     * @param string $uri
     * @param array $requestData
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     * @internal use sendRequest() instead
     */
    public function request(string $method, string $uri, array $requestData);

    /**
     * @param OneCRequest $request
     *
     * @return OneCResponse
     * @throws OneCSyncException
     * @throws OneCSyncServerErrorException
     * @throws OneCSyncClientErrorException
     */
    public function sendRequest(OneCRequest $request): OneCResponse;

    /**
     * @param OneCRequest $request
     */
    public function queueRequest(OneCRequest $request);

    /**
     * @param OneCRequest $request
     */
    public function queueRequestProrogued(OneCRequest $request);
}