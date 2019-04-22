<?php
/**
 * one-c-client.
 * Author: Andrii Yakovlev <yawa20@gmail.com>
 * Date: 22.04.19
 */
declare(strict_types=1);

namespace GepurIt\OneCClientBundle\Security;

/**
 * Class HashGenerator
 * @package GepurIt\OneCClientBundle\Security
 */
class HashGenerator
{
    /**
     * @var string
     */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @param string $content
     * @return string
     */
    public function generate(string $content)
    {
        return md5($content.$this->token);
    }
}
