<?php

namespace App\ServiceInterfaces;

interface MeterReaderInterface
{

    public function login($userId);

    public function addCustomerDetails($customerDetails);

    public function getCustomerStatus( $accountNumber);

    public function getCustomerPreviousDetails($accountNumber);

}
