<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\UtilsBundle\Tests\Util\Request;

use Contao\PageModel;
use HeimrichHannot\TestUtilitiesBundle\Mock\ModelMockTrait;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use HeimrichHannot\UtilsBundle\Tests\AbstractUtilsTestCase;
use HeimrichHannot\UtilsBundle\Util\Request\RequestUtil;
use PHPUnit\Framework\MockObject\MockBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestUtilTest extends AbstractUtilsTestCase
{
    use ModelMockTrait;

    public function getTestInstance(array $parameters = [], ?MockBuilder $mockBuilder = null)
    {
        $modelUtil = $parameters['modelUtil'] ?? $this->createMock(ModelUtil::class);
        $requestStack = $parameters['requestStack'] ?? $this->createMock(RequestStack::class);
        $kernelPackages = $parameters['kernelPackages'] ?? [];

        return new RequestUtil($modelUtil, $requestStack, $kernelPackages);
    }

    public function testGetCurrentPageModel()
    {
        unset($GLOBALS['objPage']);

        $instance = $this->getTestInstance();
        $this->assertNull($instance->getCurrentPageModel());

        $instance = $this->getTestInstance([
            'kernelPackages' => ['contao/core-bundle' => '4.4.46'],
        ]);
        $this->assertNull($instance->getCurrentPageModel());

        $instance = $this->getTestInstance([
            'kernelPackages' => ['contao/core-bundle' => '4.8.5'],
        ]);
        $this->assertNull($instance->getCurrentPageModel());

        $instance = $this->getTestInstance([
            'kernelPackages' => ['contao/core-bundle' => '4.9.5'],
        ]);
        $this->assertNull($instance->getCurrentPageModel());

        $requestStack = new RequestStack();
        $requestStack->push(new Request());
        $instance = $this->getTestInstance([
            'requestStack' => $requestStack,
            'kernelPackages' => ['contao/core-bundle' => '4.9.5'],
        ]);
        $this->assertNull($instance->getCurrentPageModel());

        $requestStack = new RequestStack();
        $request = new Request([], [], ['pageModel' => 5]);
        $requestStack->push($request);

        $instance = $this->getTestInstance([
            'requestStack' => $requestStack,
            'kernelPackages' => ['contao/core-bundle' => '4.9.5'],
        ]);
        $this->assertNull($instance->getCurrentPageModel());

        $pageModel = $this->mockModelObject(PageModel::class, ['id' => 5]);
        $requestStack = new RequestStack();
        $request = new Request([], [], ['pageModel' => $pageModel]);
        $requestStack->push($request);
        $instance = $this->getTestInstance([
            'requestStack' => $requestStack,
            'kernelPackages' => ['contao/core-bundle' => '4.9.5'],
        ]);
        $this->assertSame(5, $instance->getCurrentPageModel()->id);

        $GLOBALS['objPage'] = $pageModel;
        $requestStack = new RequestStack();
        $request = new Request([], [], ['pageModel' => 5]);
        $requestStack->push($request);

        $instance = $this->getTestInstance([
            'requestStack' => $requestStack,
            'kernelPackages' => ['contao/core-bundle' => '4.9.5'],
        ]);
        $this->assertSame(5, $instance->getCurrentPageModel()->id);

        $instance = $this->getTestInstance([
            'requestStack' => $requestStack,
            'kernelPackages' => ['contao/core-bundle' => '4.4.45'],
        ]);
        $this->assertSame(5, $instance->getCurrentPageModel()->id);

        unset($GLOBALS['objPage']);

        $modelUtil = $this->createMock(ModelUtil::class);
        $modelUtil->method('findModelInstanceByPk')->willReturn($pageModel);
        $requestStack = new RequestStack();
        $request = new Request([], [], ['pageModel' => 5]);
        $requestStack->push($request);

        $instance = $this->getTestInstance([
            'modelUtil' => $modelUtil,
            'requestStack' => $requestStack,
            'kernelPackages' => ['contao/core-bundle' => '4.9.5'],
        ]);
        $this->assertSame(5, $instance->getCurrentPageModel()->id);
    }
}
