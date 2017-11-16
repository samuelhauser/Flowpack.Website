<?php

namespace Flowpack\Website\Eel\Helper;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use Flowpack\Website\Service\GitHub;

class GitHubHelper implements ProtectedContextAwareInterface
{

    /**
     * @var GitHub
     * @Flow\Inject
     */
    protected $gitHubService;

    /**
     * Wrap the incoming string in curly brackets
     *
     * @return array
     */
    public function getMembers()
    {
        $members = $this->gitHubService->getMembers();
        return $members;
    }

    /**
     * Wrap the incoming string in curly brackets
     *
     * @return array
     */
    public function getContributors($uri)
    {

        $contributors = $this->gitHubService->getContributors($uri);
        return $contributors;
    }

    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return TRUE;
    }

}
