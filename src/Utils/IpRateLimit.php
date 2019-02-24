<?php

namespace App\Utils;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Utility class for rate-limiting by IP address.
 */
final class IpRateLimit {
    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * @var string[]
     */
    private $ipWhitelist;

    /**
     * @var int
     */
    private $maxHits;

    /**
     * @var \DateInterval
     */
    private $period;

    public function __construct(
        CacheItemPoolInterface $cacheItemPool,
        array $ipWhitelist,
        int $maxHits,
        string $period
    ) {
        $this->cacheItemPool = $cacheItemPool;
        $this->ipWhitelist = $ipWhitelist;
        $this->maxHits = $maxHits;
        $this->period = \DateInterval::createFromDateString($period);
    }

    public function isExceeded(string $ip): bool {
        if ($this->isWhitelisted($ip)) {
            return false;
        }

        $cacheItem = $this->cacheItemPool->getItem(self::getCacheKey($ip));

        if (!$cacheItem->isHit()) {
            return false;
        }

        return $cacheItem->get() > $this->maxHits;
    }

    public function increment(string $ip): void {
        if ($this->isWhitelisted($ip)) {
            return;
        }

        $cacheItem = $this->cacheItemPool->getItem(self::getCacheKey($ip));
        $cacheItem->set(($cacheItem->get() ?? 0) + 1);
        $cacheItem->expiresAfter($this->period);

        $this->cacheItemPool->saveDeferred($cacheItem);
    }

    public function reset(string $ip): void {
        $this->cacheItemPool->deleteItem(self::getCacheKey($ip));
    }

    public function clear(): void {
        $this->cacheItemPool->clear();
    }

    private function isWhitelisted(string $ip): bool {
        return IpUtils::checkIp($ip, $this->ipWhitelist);
    }

    private static function getCacheKey(string $ip): string {
        return \str_replace(':', 'x', $ip);
    }
}
