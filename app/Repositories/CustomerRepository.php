<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\ExistingCustomer;
use App\ServiceInterfaces\CustomerInterface;

class CustomerRepository implements CustomerInterface
{

    public function getCustomerDetails($accountNumber)
    {
        return Customer::where('account_number', '=', $accountNumber)->orderBy('reading_date', 'desc')->take(2)->get()->toArray();
    }

    public function getCustomerStatus($accountNumber)
    {
        try {
            return ExistingCustomer::where('account_number', '=', $accountNumber)->exists();
        } catch (\Exception $e){
            return $e->getCode();
        }
    }
}
