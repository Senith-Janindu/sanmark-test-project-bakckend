<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\ExistingCustomer;
use App\Models\MeterReader;
use App\ServiceInterfaces\MeterReaderInterface;

class MeterReaderRepository implements MeterReaderInterface
{

    public function login($userId)
    {
        try {
            $meterReader = MeterReader::where('user_id', '=', $userId)->exists();
            if ($meterReader) {
                return MeterReader::where('user_id', '=', $userId)->first();
            }else{
                return false;
            }
        } catch (\Exception $e){
            return $e->getCode();
        }
    }

    public function addCustomerDetails($customerDetails)
    {
        try {
            $newCustomerDetails = new Customer();
            $newCustomerDetails->fill($customerDetails);
            $newCustomerDetails->save();
            return true;
        } catch (\Exception $e){
            return $e->getCode();
        }
    }

    public function getCustomerStatus($accountNumber)
    {
        try {
            return ExistingCustomer::where('account_number', '=', $accountNumber)->exists();
        } catch (\Exception $e){
            return $e->getCode();
        }
    }

    public function getCustomerPreviousDetails($accountNumber)
    {
        try {
            return Customer::where('account_number', '=', $accountNumber)->orderBy('reading_date', 'desc')->first();
        } catch (\Exception $e){
            return $e->getCode();        }
    }
}
