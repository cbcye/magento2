<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Test\Tools\Dependency\Report;

use Magento\Tools\Dependency\ServiceLocator;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $fixtureDir;

    /**
     * @var string
     */
    protected $fixtureDirModule;

    /**
     * @var string
     */
    protected $sourceFilename;

    /**
     * @var \Magento\Tools\Dependency\Report\BuilderInterface
     */
    protected $builder;

    protected function setUp()
    {
        $this->fixtureDir = realpath(__DIR__ . '/../_files') . '/';
        $this->fixtureDirModule = $this->fixtureDir . 'code/Magento/FirstModule/';
        $this->sourceFilename = $this->fixtureDir . 'framework-dependencies.csv';

        $this->builder = ServiceLocator::getFrameworkDependenciesReportBuilder();
    }

    public function testBuild()
    {
        $this->builder->build([
            'parse' => [
                'files_for_parse' => [
                    $this->fixtureDirModule . 'Helper/Helper.php',
                    $this->fixtureDirModule . 'Model/Model.php',
                    $this->fixtureDirModule . 'view/frontend/template.phtml',
                ],
                'config_files' => [
                    $this->fixtureDirModule . 'etc/module.xml',
                ],
                'declared_namespaces' => ['Magento'],
            ],
            'write' => [
                'report_filename' => $this->sourceFilename,
            ],
        ]);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/framework-dependencies.csv',
            $this->sourceFilename
        );
    }

    public function testBuildWithoutDependencies()
    {
        $this->builder->build([
            'parse' => [
                'files_for_parse' => [
                    $this->fixtureDirModule . 'Model/WithoutDependencies.php',
                ],
                'config_files' => [
                    $this->fixtureDirModule . 'etc/module.xml',
                ],
                'declared_namespaces' => ['Magento'],
            ],
            'write' => [
                'report_filename' => $this->sourceFilename,
            ],
        ]);

        $this->assertFileEquals(
            $this->fixtureDir . 'expected/without-framework-dependencies.csv',
            $this->sourceFilename
        );
    }

    public function tearDown()
    {
        if (file_exists($this->sourceFilename)) {
            unlink($this->sourceFilename);
        }
    }
}
