<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 17/09/2018
     * Time: 21:20
     */

    namespace App\Utils;

    class BlockDataGenesis implements BlockDataInterface
    {
        public const GENESIS_BLOCK_NAME = 'Genesis Block';

        public function __toString() : string
        {
            return self::GENESIS_BLOCK_NAME;
        }

        public function getData(): string
        {
            return (string) $this;
        }

        public function isValid() : bool
        {
            return true;
        }
    }
