<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\GlueJsonApi\Plugin;

use Spryker\Glue\GlueApplication\ApiApplication\ApiApplicationContext;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ApiApplicationContextExpanderPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

class HostApplicationApiContextExpander extends AbstractPlugin implements ApiApplicationContextExpanderPluginInterface
{
    public const HOST = 'host';

    /**
     * @param ApiApplicationContext $apiApplicationContext
     *
     * @return ApiApplicationContext
     */
    public function expand(ApiApplicationContext $apiApplicationContext): ApiApplicationContext
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $apiApplicationContext->set(static::HOST, $_SERVER['HTTP_HOST']);
        }

        return $apiApplicationContext;
    }
}
