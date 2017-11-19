<?php

namespace Flowpack\Website\Aspects;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;
use Neos\Cache\Frontend\VariableFrontend;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class GitHubCachingAspect
{
    /**
     * @Flow\Inject
     * @var VariableFrontend
     */
    protected $cache;

    /**
     * @Flow\Around("method(Flowpack\Website\Service\GitHub->getMembers())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function cacheGetMembers(JoinPointInterface $joinPoint)
    {
        $cacheIdentifier = md5('https://api.github.com/orgs/Flowpack/members');

        if ($this->cache->has($cacheIdentifier)) {
            $members = $this->cache->get($cacheIdentifier);
        } else {
            $members = $joinPoint->getAdviceChain()->proceed($joinPoint);
            $this->cache->set($cacheIdentifier, $members);
        }

        return $members;
    }

    /**
     * @Flow\Around("method(Flowpack\Website\Service\GitHub->getContributors())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function cacheGetContributors(JoinPointInterface $joinPoint)
    {
        $uri = $joinPoint->getMethodArgument('uri');
        $cacheIdentifier = md5($uri);

        if ($this->cache->has($cacheIdentifier)) {
            $contributors = $this->cache->get($cacheIdentifier);
        } else {
            $contributors = $joinPoint->getAdviceChain()->proceed($joinPoint);
            $this->cache->set($cacheIdentifier, $contributors);
        }

        return $contributors;
    }

    /**
     * @Flow\Around("method(Flowpack\Website\Service\GitHub->getUser())")
     * @param JoinPointInterface $joinPoint The current join point
     * @return mixed
     */
    public function cacheGetUser(JoinPointInterface $joinPoint)
    {
        $login = $joinPoint->getMethodArgument('login');
        $cacheIdentifier = md5($login);

        if ($this->cache->has($cacheIdentifier)) {
            $user = $this->cache->get($cacheIdentifier);
        } else {
            $user = $joinPoint->getAdviceChain()->proceed($joinPoint);
            $this->cache->set($cacheIdentifier, $user);
        }

        return $user;
    }
}
