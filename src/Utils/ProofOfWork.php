<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 12/09/2018
     * Time: 20:08
     */

    namespace App\Utils;

    /**
     * Let's slow down, in order for you to proof you spent some amount of time
     * computing the hash.
     *
     * Class ProofOfWork
     * @package App\Utils
     */
    class ProofOfWork
    {
        private const VALUE_TO_ASSERT_IN_HASH = '1111';
        private const HASH_ALGORITHM          = 'sha256';

        /**
         * In cryptography, a nonce is an arbitrary number that can be used just once.
         * It is similar in spirit to a nonce word, hence the name.
         * It is often a random or pseudo-random number.
         *
         * @see https://en.bitcoin.it/wiki/Nonce
         *
         * @var string
         */
        protected $nonce;

        /** @var string */
        protected $data;

        /**
         * ProofOfWork constructor.
         * @param string $data
         */
        public function __construct(string $data)
        {
            $this->data  = $data;
            $this->nonce = 0;
        }

        /**
         * @return string
         */
        public function getNonce() : string {
            while (!$this->isNonceValid()) {
                ++$this->nonce;
            }

            return $this->nonce;
        }

        /**
         * @return bool
         */
        public function isNonceValid() : bool {
            // more values we add, more difficult to to find -> delay
            return 0 === strpos(\hash(self::HASH_ALGORITHM, $this->dataToBeHashed()), self::VALUE_TO_ASSERT_IN_HASH);
        }

        /**
         * @return string
         */
        public function dataToBeHashed() : string {
            return $this->data . $this->nonce;
        }

        /**
         * @return string
         */
        public function getHash() : string {
            $this->getNonce();

            return \hash(self::HASH_ALGORITHM, $this->dataToBeHashed());
        }

        /**
         * @param string $hash
         * @return bool
         */
        public function validateHash(string $hash) : bool {
            return $hash === $this->getHash();
        }
    }