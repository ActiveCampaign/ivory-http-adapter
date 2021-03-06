<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Cache\Adapter;

use Ivory\HttpAdapter\Event\Cache\Adapter\DoctrineCacheAdapter;

/**
 * Doctrine cache adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DoctrineCacheAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\Cache\Adapter\DoctrineCacheAdapter */
    private $doctrineCacheAdapter;

    /** @var \Doctrine\Common\Cache\Cache|\PHPUnit_Framework_MockObject_MockObject */
    private $cache;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->cache = $this->createCacheMock();
        $this->doctrineCacheAdapter = new DoctrineCacheAdapter($this->cache);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->cache);
        unset($this->doctrineCacheAdapter);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface',
            $this->doctrineCacheAdapter
        );
    }

    public function testHas()
    {
        $this->cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue(true));

        $this->assertTrue($this->doctrineCacheAdapter->has($id));
    }

    public function testGetWithValidId()
    {
        $this->cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue(true));

        $this->cache
            ->expects($this->once())
            ->method('fetch')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue($data = 'data'));

        $this->assertSame($data, $this->doctrineCacheAdapter->get($id));
    }

    public function testGetWithInvalidId()
    {
        $this->cache
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue(false));

        $this->cache
            ->expects($this->never())
            ->method('fetch');

        $this->assertNull($this->doctrineCacheAdapter->get($id));
    }

    public function testSet()
    {
        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->identicalTo($id = 'id'),
                $this->identicalTo($data = 'data'),
                $this->identicalTo($lifeTime = 123)
            )
            ->will($this->returnValue(true));

        $this->assertTrue($this->doctrineCacheAdapter->set($id, $data, $lifeTime));
    }

    public function testRemove()
    {
        $this->cache
            ->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue(true));

        $this->assertTrue($this->doctrineCacheAdapter->remove($id));
    }

    /**
     * Creates a cache mock.
     *
     * @return \Doctrine\Common\Cache\Cache|\PHPUnit_Framework_MockObject_MockObject The cache mock.
     */
    private function createCacheMock()
    {
        return $this->getMock('Doctrine\Common\Cache\Cache');
    }
}
