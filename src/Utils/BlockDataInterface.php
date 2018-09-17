<?php
    /**
     * Created by PhpStorm.
     * User: Filipe <filipefernandes007@gmail.com>
     * Date: 17/09/2018
     * Time: 11:51
     */

    namespace App\Utils;

    /**
     * The approach i choose demands a certain type of data to be
     * accepted in Block creation, and that data must implement this interface.
     * Of course you can choose to add/drop methods, but if you do,
     * take a look at Block before making choices that broke the code.
     *
     * Interface BlockDataInterface
     * @package App\Utils
     */
    interface BlockDataInterface
    {
        public function __toString() : string;
        public function isValid() : bool;
        public function getData() : string;
    }