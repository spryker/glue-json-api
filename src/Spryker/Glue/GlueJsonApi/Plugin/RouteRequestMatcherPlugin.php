<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApi\Plugin;

use Spryker\Glue\GlueApplication\Request\ApiRequestInterface;
use Spryker\Glue\GlueApplication\Resource\MissingResource;
use Spryker\Glue\GlueApplication\Resource\Resource;
use Spryker\Glue\GlueApplication\Resource\ResourceInterface;
use Spryker\Glue\GlueJsonApiExtension\Dependency\Plugin\ResourceRoutePluginInterface;

class RouteRequestMatcherPlugin
{
    /**
     * @var ResourceRoutePluginInterface[]
     */
    protected $resourceRouteCollection;

    /**
     * @param ResourceRoutePluginInterface[] $resourceRouteCollection
     */
    public function __construct(array $resourceRouteCollection)
    {
        $this->resourceRouteCollection = $resourceRouteCollection;
    }

    /**
     * @param ApiRequestInterface $apiRequest
     *
     * @return ResourceInterface
     */
    public function matchRequest(ApiRequestInterface $apiRequest): ResourceInterface
    {
        foreach ($this->resourceRouteCollection as $resourceRoute) {
            if ($this->isMethodMatching($resourceRoute, $apiRequest) && $this->isPathMatching($resourceRoute, $apiRequest)) {
                return new Resource(function (...$arguments) use ($resourceRoute) {
                    //@todo use a controller resolver here
                    $controllerClass = $resourceRoute->getControllerClass();
                    $controller = new $controllerClass();

                    return call_user_func([
                        $controller,
                        $resourceRoute->getAction(), //@todo error handling when method does not exists
                    ], ...$arguments);
                });
            }
        }

        return new MissingResource(
            '404',
            sprintf('Route %s %s could not be found', $apiRequest->getMethod(), $apiRequest->getPath())
        );
    }

    /**
     * @param ResourceRoutePluginInterface $resourceRoute
     * @param ApiRequestInterface $apiRequest
     *
     * @return bool
     */
    protected function isMethodMatching(ResourceRoutePluginInterface $resourceRoute, ApiRequestInterface $apiRequest): bool
    {
        return $resourceRoute->getMethod() === $apiRequest->getMethod();
    }

    /**
     * @param ResourceRoutePluginInterface $resourceRoute
     * @param ApiRequestInterface $apiRequest
     *
     * @return bool
     */
    protected function isPathMatching(ResourceRoutePluginInterface $resourceRoute, ApiRequestInterface $apiRequest): bool
    {
        //@todo very simple implementation, which does not care about rest standards, sub-resources, versioning, etc. Only for PoC.
        return $resourceRoute->getPath() === $apiRequest->getPath();
    }
}
