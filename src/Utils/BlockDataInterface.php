<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 17/09/2018
     * Time: 11:51
     */

    namespace App\Utils;

    interface BlockDataInterface
    {
        public function __toString() : string;
        public function isValid() : bool;
        public function getData() : string;
    }