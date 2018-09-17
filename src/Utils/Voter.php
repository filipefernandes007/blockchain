<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 17/09/2018
     * Time: 12:01
     */

    namespace App\Utils;

    class Voter implements BlockDataInterface
    {
        public const YES = 'YES';
        public const NO  = 'NO';

        /** @var int */
        public $voterId;

        /** @var string */
        public $yesNo;

        public function __toString() : string
        {
            return serialize($this);
        }

        public function isValid() : bool
        {
            // Implement isValid() method as you want
            return $this->yesNo === self::YES || $this->yesNo === self::NO;
        }

        public function getData() : string {
            return (string) $this;
        }
    }