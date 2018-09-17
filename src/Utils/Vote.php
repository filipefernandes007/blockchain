<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 17/09/2018
     * Time: 12:01
     */

    namespace App\Utils;

    class Vote implements BlockDataInterface
    {
        public const YES = 'YES';
        public const NO  = 'NO';

        /** @var int */
        protected $voterId;

        /** @var string */
        protected $yesNo;

        /**
         * Vote constructor.
         * @param int         $id
         * @param string|null $vote
         */
        public function __construct(int $id, string $vote = null)
        {
            $this->voterId = $id;
            $this->yesNo   = $vote === self::YES || $vote === self::NO ? $vote : '';
        }

        /**
         * @return int
         */
        public function getVoterId() : int
        {
            return $this->voterId;
        }

        /**
         * @param int $voterId
         * @return Vote
         */
        public function setVoterId(int $voterId) : Vote
        {
            $this->voterId = $voterId;

            return $this;
        }

        /**
         * @return string
         */
        public function getYesNo() : string
        {
            return $this->yesNo;
        }

        /**
         * @param string $yesNo
         * @return Vote
         */
        public function setYesNo(string $yesNo) : Vote
        {
            $this->yesNo = $yesNo;

            return $this;
        }

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