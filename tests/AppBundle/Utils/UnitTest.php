<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 13/09/2018
     * Time: 09:35
     */

    namespace App\Tests\AppBundle\Utils;

    use App\Utils\ProofOfWork;
    use PHPUnit\Framework\TestCase;

    class UnitTest extends TestCase
    {
        public function testProofOfWork() : void {
            $pow  = new ProofOfWork('Hello');
            $hash = $pow->getHash();

            $this->assertEquals('111179920ed942d05fd14f176c8d9d531b5b659e8ceaa5d1523f2ad8b54fa1a3', $hash);
            $this->assertTrue($pow->validateHash($hash));
            $this->assertTrue($pow->isNonceValid());
            $this->assertEquals(63954, $pow->getNonce());
        }

    }