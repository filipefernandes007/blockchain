<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 10/09/2018
     * Time: 17:07
     */

    namespace App\Tests\AppBundle\Entity;

    use App\DataFixtures\BlockchainDataFixtures;
    use App\Entity\Blockchain;
    use App\Utils\BlockchainTestEntity;
    use App\Utils\BlockDataGenesis;
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

            $fixture = new BlockchainDataFixtures();
            $fixture->load($this->objectManager);

            parent::setUp();
        }

        public function testTrue() : void {
            parent::assertTrue(true);
        }

        /**
         * @throws \Doctrine\ORM\NonUniqueResultException
         */
        public function testBlockchainNotValidBySwappingHashValues() : void {
            $blockchain = $this->getLastBlockchain();

            $hash = $blockchain->genesisBlock()->getHash();

            // swap hash's in block cause blockchain to be not valid
            $blockchain->genesisBlock()->setHash($blockchain->getBlock(1)->getHash());
            $blockchain->getBlock(1)->setHash($hash);

            parent::assertFalse($blockchain->isValid());
        }

        /**
         * @throws \Doctrine\ORM\NonUniqueResultException
         */
        public function testBlockchainNotValidBySwappingPreviousBlockHash() : void {
            $blockchain = $this->getLastBlockchain();

            $blockchain->getBlock(1)->setPreviousBlockHash($blockchain->getBlock(2)->getHash());
            $blockchain->getBlock(2)->setPreviousBlockHash($blockchain->getBlock(1)->getHash());

            parent::assertFalse($blockchain->isValid());
        }

        /**
         * @throws \Exception
         */
        public function testBlockchainNotValidByShuffleBlocks() : void {
            $blockchain = $this->getLastBlockchain();
            $blocks     = $blockchain->getBlocks()->toArray();

            $blockchainTest = new BlockchainTestEntity();

            // first, verify if it's valid
            $blockchainTest->setBlocks($blocks);
            parent::assertTrue($blockchainTest->isValid());

            \shuffle($blocks);
            $blockchainTest->setBlocks($blocks);
            parent::assertFalse($blockchainTest->isValid());
        }

        /**
         * @throws \Doctrine\ORM\NonUniqueResultException
         */
        public function testBlockchainIsValid() : void {
            $blockchain = $this->getLastBlockchain();
            parent::assertNotNull($blockchain);
            parent::assertTrue($blockchain->isValid());
        }

        /**
         * @throws \Doctrine\ORM\NonUniqueResultException
         */
        public function testBlocksInBlockchain() : void {
            $blockchain = $this->getLastBlockchain();
            $blocks     = $blockchain->getBlocks();

            parent::assertEquals(BlockDataGenesis::GENESIS_BLOCK_NAME, $blocks[0]->getData());
            parent::assertCount(6, $blocks);
        }

        /**
         * @return Blockchain
         * @throws \Doctrine\ORM\NonUniqueResultException
         */
        private function getLastBlockchain() : Blockchain {
            return $this->objectManager
                        ->getRepository(Blockchain::class)
                        ->getLastEntity();
        }
    }
