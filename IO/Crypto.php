<?php

namespace Yonna\IO;

use Dotenv\Exception\ValidationException;
use Yonna\Foundation\Str;
use Yonna\Response\Collector;

class Crypto
{

    /**
     * 是否隐秘请求
     * @param Request $request
     * @return bool
     */
    public static function isCrypto(Request $request): bool
    {
        return $request->getInputType() === InputType::RAW && strpos($request->getRawData(), Config::getCryptoProtocol()) === 0;
    }

    /**
     * @param $str
     * @return string
     */
    private static function encrypt(string $str)
    {
        $type = Config::getCryptoType();
        $secret = Config::getCryptoSecret();
        $iv = Config::getCryptoIv();
        if (!$type || !$secret || !$iv) {
            return $str;
        }
        return openssl_encrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * @param $str
     * @return string
     */
    private static function decrypt(string $str)
    {
        $type = Config::getCryptoType();
        $secret = Config::getCryptoSecret();
        $iv = Config::getCryptoIv();
        if (!$type || !$secret || !$iv) {
            return $str;
        }
        return openssl_decrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * 处理input
     * @param Request $request
     * @return Request
     */
    public static function input(Request $request)
    {
        if (self::isCrypto($request) === false) {
            $raw = $request->getRawData() ? json_decode($request->getRawData(), true) : [];
        } else {
            $raw = self::decrypt(Str::replaceFirst(Config::getCryptoProtocol(), '', $request->getRawData()));
            $raw = $raw ? json_decode($raw, true) : [];
        }
        if (isset($raw['client_id'])) {
            $request->setClientId($raw['client_id']);
            unset($raw['client_id']);
        }
        $scopes = [];
        if (isset($raw['scopes'])) {
            $scopes = $raw['scopes'];
            unset($raw['scopes']);
        } else if (isset($raw['scope'])) {
            $scopes = [$raw['scope'] => $raw];
            unset($raw['scope']);
        }
        if (empty($scopes)) {
            $request_uri = $request->getRequestUri();
            if ($request_uri) {
                $rArr = explode('/', $request_uri);
                $rArr = array_filter($rArr);
                $s = array_shift($rArr);
                $scopes = [$s => ['params' => $rArr]];
            }
        }
        $request->setScopes($scopes);
        unset($raw);
        return $request;
    }

    /**
     * 处理output
     * @param Request $request
     * @param Collector $collector
     * @return Collector
     */
    public static function output(Request $request, Collector $collector)
    {
        if (self::isCrypto($request) === false) {
            return $collector;
        }
        $data = ['crypto' => Config::getCryptoProtocol() . self::encrypt(json_encode($collector->getData()))];
        $collector->setMsg('fetch crypto success');
        $collector->setData($data);
        return $collector;
    }


}