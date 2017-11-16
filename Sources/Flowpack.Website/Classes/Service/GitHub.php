<?php
namespace Flowpack\Website\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Flow\Log\SystemLoggerInterface;
use Neos\Utility\Arrays;

/**
 * @Flow\Scope("singleton")
 */
class GitHub
{
    const ORGANIZATION = 'Flowpack';

    const CONTRIBUTORS_URI_PATTERN = 'https://api.github.com/repos/Flowpack/@PACKAGE_KEY@/contributors';

    /**
     * @var SystemLoggerInterface
     * @Flow\Inject
     */
    protected $logger;

    /**
     * @var Browser
     * @Flow\Inject
     */
    protected $client;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @Flow\InjectConfiguration(path="github")
     * @var string
     */
    protected $githubSettings;

    public function initializeObject()
    {
        $this->client->setRequestEngine(new CurlEngine());
        $this->client->addAutomaticRequestHeader('Authorization', 'token ' . $this->githubSettings['token']);
    }

    protected function fetch(string $uri): array
    {
        $teamMembers = [];
        $dataObj = $this->client->request($uri);
        $dataObj = \json_decode($dataObj, true);

        if (isset($this->cache[$uri])) {
            return $this->cache[$uri];
        }

        foreach ($dataObj as $data) {
            $user = $this->client->request('https://api.github.com/users/' . $data['login']);
            $user = \json_decode($user, true);

            $teamMembers[] = [
                'name' => $user['name'],
                'login' => $user['login'],
                'avatar_url' => $user['avatar_url'],
                'url' => $user['html_url']
            ];
        }

        $this->cache[$uri] = $teamMembers;
        return $this->cache[$uri];
    }

    public function getMembers(): array
    {
        try {
            return $this->fetch('https://api.github.com/orgs/Flowpack/members');
        } catch (\Exception $exception) {
            $this->logger->logException($exception);
        }
        return [];
    }

    public function getContributors(string $uri): array
    {
        try {
            $githubPackageName = \str_replace('/' . self::ORGANIZATION . '/', '', parse_url($uri, PHP_URL_PATH));
            $githubUri = \str_replace('@PACKAGE_KEY@', $githubPackageName, self::CONTRIBUTORS_URI_PATTERN);
            return $this->fetch($githubUri);
        } catch (\Exception $exception) {
            $this->logger->logException($exception);
        }
        return [];
    }
}
