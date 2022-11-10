<?php

namespace App\Service;

use Psr\Cache\CacheItemInterface;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MixRepository
{
//    private HttpClientInterface $httpClient;
//    private CacheInterface $cache;
//
//    // Dependencies (Dependency Injection)
//    public function __construct(HttpClientInterface $httpClient, CacheInterface $cache)
//    {
//        $this->httpClient = $httpClient;
//        $this->cache = $cache;
//    }

    // Other way to make a construct
    public function __construct(
        private readonly HttpClientInterface $gitHubContentClient,
        private readonly CacheInterface $cache,

        #[Autowire('%kernel.debug%')]
        private readonly bool $isDebug,

        /*
        #[Autowire(service: 'twig.command.debug')]
        private DebugCommand $twigDebugCommand
        */
    ){}

    public function findAll(): array
    {
        /*
        $output = new BufferedOutput();
        $this->twigDebugCommand->run(new ArrayInput([]), $output);
        dd($output);
        */

        return $this->cache->get('mixes_data', function (CacheItemInterface $cacheItem) {
            $cacheItem->expiresAfter($this->isDebug ? 5 : 60);
            $response = $this->gitHubContentClient->request('GET', '/SymfonyCasts/vinyl-mixes/main/mixes.json');

            return $response->toArray();
        });
    }
}