<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 17/09/2018
     * Time: 21:11
     */

    namespace App\DataFixtures;

    use App\Entity\Block;
    use App\Entity\Blockchain;
    use App\Utils\BlockDataGenesis;
    use App\Utils\Vote;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\Persistence\ObjectManager;

    class BlockchainDataFixtures extends Fixture
    {
        /**
         * @param ObjectManager $manager
         * @throws \Exception
         */
        public function load(ObjectManager $manager)
        {
            // maybe it didn't purge...
            $blockhains = $manager->getRepository(Blockchain::class)
                                  ->findAll();

            foreach ($blockhains As $b) {
                $manager->remove($b);
            }

            $manager->flush();

            $blockhain = new Blockchain(new Block(new BlockDataGenesis()));
            $manager->persist($blockhain);
            $manager->flush();

            /** @var Vote[] $voters */
            $voters = $this->getVotes();

            /** @var Block[] $blocks */
            $blocks = [$blockhain->genesisBlock()];

            foreach ($voters as $vote) {
                $block = new Block($vote, $this->lastBlock($blocks));
                $manager->persist($block);

                $blockhain->addBlock($block);

                $blocks[] = $block;
            }

            $manager->persist($blockhain);
            $manager->flush();
        }

        private function lastBlock(array $blocks) : ?Block {
            $totalBlocks = \count($blocks);

            if ($totalBlocks === 0) {
                return null;
            }

            return $blocks[$totalBlocks - 1];
        }

        private function getVotes() : array {
            return [
                new Vote($this->randomUniqueId(), Vote::YES),
                new Vote($this->randomUniqueId(), Vote::NO),
                new Vote($this->randomUniqueId(), null),
                new Vote($this->randomUniqueId(), Vote::YES),
                new Vote($this->randomUniqueId(), Vote::NO),
            ];
        }

        private function randomUniqueId() : int {
            return (new \DateTime())->getTimestamp();
        }
    }