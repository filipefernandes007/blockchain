<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 10/09/2018
     * Time: 17:07
     */

    namespace App\Tests\AppBundle\Entity;

    use App\Entity\Blockchain;
    use Doctrine\Common\Persistence\ObjectManager;
    use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
    use App\Kernel;
    use Symfony\Component\DependencyInjection\Container;

    /**
     * Class BlockchainTest
     * @package App\Tests\AppBundle\Entity
     */
    class BlockchainTest extends TestCase
    {
        /** @var Kernel */
        private $kernel;

        /** @var Container */
        private $container;

        /** @var ObjectManager */
        private $objectManager;

        /**
         * @throws \Exception
         */
        public function setUp()
        {
            $this->kernel = new \App\Kernel('test', true);
            $this->kernel->boot();

            // Store the container and the entity manager in test case properties
            $this->container     = $this->kernel->getContainer();
            $this->objectManager = $this->container->get('doctrine')->getManager();

            parent::setUp();
        }

        public function testBlockchainNotValidBySwappingHashValues() : void {
            $blockchain = $this->objectManager->getRepository(Blockchain::class)->find(1);
            $hash = $blockchain->getBlocks()[0]->getHash();

            // swap hash's in block cause blockchain to be not valid
            $blockchain->getBlocks()[0]->setHash($blockchain->getBlocks()[1]->getHash());
            $blockchain->getBlocks()[1]->setHash($hash);

            parent::assertFalse($blockchain->isValid());
        }

        public function testBlockchainNotValidBySwappingPreviousBlockHash() : void {
            $blockchain = $this->objectManager->getRepository(Blockchain::class)->find(1);

            $blockchain->getBlocks()[1]->setPreviousBlockHash($blockchain->getBlocks()[2]->getHash());
            $blockchain->getBlocks()[2]->setPreviousBlockHash($blockchain->getBlocks()[1]->getHash());

            parent::assertFalse($blockchain->isValid());
        }

        /**
         * @throws \Exception
         */
        public function testBlockchainNotValidByShuffleBlocks() : void {
            $blockchain = $this->objectManager->getRepository(Blockchain::class)->find(1);
            $blocks     = $blockchain->getBlocks()->toArray();

            /** @var BlockchainTestEntity $blockchainTest */
            $blockchainTest = new BlockchainTestEntity();

            // first, verify if it's valid
            $blockchainTest->setBlocks($blocks);
            parent::assertTrue($blockchainTest->isValid());

            \shuffle($blocks);
            $blockchainTest->setBlocks($blocks);
            parent::assertFalse($blockchainTest->isValid());
        }

        public function testBlockchainIsValid() : void {
            /** @var Blockchain $blockchain */
            $blockchain = $this->objectManager->getRepository(Blockchain::class)->find(1);
            parent::assertNotNull($blockchain);
            parent::assertTrue($blockchain->isValid());
        }

        public function testBlocksInBlockchain() : void {
            $blockchain = $this->objectManager->getRepository(Blockchain::class)->find(1);
            $blocks     = $blockchain->getBlocks();

            parent::assertEquals('Genesis Test', $blocks[0]->getData());
            parent::assertCount(3, $blocks);
        }
    }
