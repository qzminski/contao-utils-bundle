<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\UtilsBundle\Tests\Model;

use Contao\Database;
use Contao\System;
use Contao\TestCase\ContaoTestCase;
use DataContainer;
use HeimrichHannot\UtilsBundle\Model\CfgTagModel;

class CfgTagModelTest extends ContaoTestCase
{
    public function testGetSourcesAsOptions()
    {
        $container = $this->mockContainer();
        $container->set('contao.framework', $this->mockContaoFramework([Database::class => $this->getDatabaseMock()]));
        System::setContainer($container);
        $options = CfgTagModel::getSourcesAsOptions($this->getDataContainerMock());
        $this->assertSame([1 => 'aoptions', 0 => 'options'], $options);
    }

    public function getDatabaseMock()
    {
        $databaseAdapter = $this->mockAdapter([
            'getInstance',
            'prepare',
            'execute',
            'fetchEach',
        ]);
        $databaseAdapter->method('getInstance')->willReturnSelf();
        $databaseAdapter->method('prepare')->withAnyParameters()->willReturnSelf();
        $databaseAdapter->method('fetchEach')->willReturn(['options', 'aoptions']);
        $databaseAdapter->method('execute')->willReturnSelf();

        return $databaseAdapter;
    }

    /**
     * @return DataContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getDataContainerMock()
    {
        return $this->createMock(DataContainer::class);
    }
}
