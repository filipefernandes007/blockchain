<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 14/09/2018
     * Time: 02:05
     */

    namespace App\Tests\AppBundle\Entity;

    use App\Entity\Blockchain;

    /**
     * Class BlockchainTestEntity
     * @package App\Tests\AppBundle\Entity
     */
    class BlockchainTestEntity extends Blockchain
    {
        /**
         * This is the only method that parent class does not have - for all security reasons!
         *
         * @param array $blocks
         * @return BlockchainTestEntity
         */
        public function setBlocks(array $blocks) : self {
            $this->blocks = $blocks;

            return $this;
        }
    }