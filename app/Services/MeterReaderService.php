<?php

namespace App\Services;

use App\Models\Customer;
use App\ServiceInterfaces\MeterReaderInterface;
use Carbon\Carbon;
use ErrorException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Psy\Exception\TypeErrorException;

class MeterReaderService
{
    protected MeterReaderInterface $meterReaderInterface;

    public function __construct(MeterReaderInterface $meterReaderInterface)
    {
        $this->meterReaderInterface = $meterReaderInterface;
    }

    public function login($loginData)
    {
        if (Auth::guard('meter_readers')->attempt(['user_id' => $loginData['user_id'], 'password' => $loginData['password']])) {
            $meterReader = $this->meterReaderInterface->login($loginData['user_id']);
            $token = $meterReader->createToken('myapptoken')->plainTextToken;
            return [
                'status' => true,
                'message' => 'Successfully logged in',
                'data' => $token,
            ];
        } else {
            return [
            'status' => false,
                'message' => 'Login failed',
                'data' => null,
            ];
        }
    }

    public function addCustomerDetails($customerInputDetails)
    {
        $accountNumber =  $customerInputDetails['account_number'];
        $customerStatus = $this->meterReaderInterface->getCustomerStatus($accountNumber);
        if ($customerStatus){
            $customerPreviousDetails = $this->meterReaderInterface->getCustomerPreviousDetails($accountNumber);
            $customerPreviousValues = $this->assignValues($customerPreviousDetails);
            $calculateValues = $this->amountCalculation($customerInputDetails , $customerPreviousValues);

            if (!$calculateValues) {
                return [
                    'status' => false,
                    'message' => 'Meter reading should be greater than previous reading',
                    'data' => null,
                ];
            } else {
                $customerDetails = [
                    'account_number' => $customerInputDetails['account_number'],
                    'reading_date' => $customerInputDetails['reading_date'],
                    'reading_value' => $customerInputDetails['reading_value'],
                    'fixed_charge' => $calculateValues['fixed_charge'],
                    'first_range_amount' => $calculateValues['first_range_amount'],
                    'second_range_amount' => $calculateValues['second_range_amount'],
                    'third_range_amount' =>  $calculateValues['third_range_amount'],
                    'total_amount' => $calculateValues['total_amount']
                ];
                $addCustomerDetailsStatus = $this->meterReaderInterface->addCustomerDetails($customerDetails);

                return [
                    'status' => $addCustomerDetailsStatus,
                    'message' => 'Successfully added customer data',
                    'data' => null,
                ];
            }

        } else {
            return [
                'status' => false,
                'message' => "Customer doesn't exist",
                'data' => null,
            ];
        }
    }

    public function assignValues($customerPreviousDetails)
    {
        if ($customerPreviousDetails == null){
            $customerPreviousValues = [
                'previous_reading_date' => Carbon::now()->toDateString(),
                'previous_reading_value' => 0
            ];
        } else {
            $customerPreviousValues = [
                'previous_reading_date' => $customerPreviousDetails['reading_date'],
                'previous_reading_value' => $customerPreviousDetails['reading_value'],
            ];
        }
        return $customerPreviousValues;
    }

    public function amountCalculation($customerInputDetails , $customerPreviousValues)
    {
        $readingDifference = $customerInputDetails['reading_value'] - $customerPreviousValues['previous_reading_value'];

        if ($readingDifference > 0) {
            $readingDate = Carbon::parse($customerInputDetails['reading_date']);
            $previousReadingDate = Carbon::parse($customerPreviousValues['previous_reading_date']);

            $dateDifference = $readingDate->diffInDays($previousReadingDate);

            $unitsSecondRange = 0;
            $unitsThirdRange = 0;
            $fixedCharge = 0;

            $unitsFirstRange = $dateDifference;

            if( $readingDifference > $unitsFirstRange){
                $unitsSecondRange = 2 * $dateDifference;
            }

            $unitsThirdRange = $readingDifference - ($unitsFirstRange + $unitsSecondRange);

            if($readingDifference <= $unitsFirstRange){
                $fixedCharge = 500;
            }elseif(0 < $unitsSecondRange){
                $fixedCharge = 1000;
            }

            $chargeForFirstRange = $unitsFirstRange * 20;
            $chargeForSecondRange = $unitsSecondRange * 35;

            $chargeForThirdRange = 0;

            if ($unitsThirdRange > 0) {
                $fixedCharge = 1500;
                $perUnitCharge = 40;

                for ($i = 0; $i < $unitsThirdRange; $i++) {
                    $chargeForThirdRange += $perUnitCharge;
                    $perUnitCharge++;
                }
            }

            $totalAmount = $chargeForFirstRange + $chargeForSecondRange + $chargeForThirdRange + $fixedCharge;

            return [
                'fixed_charge' => $fixedCharge,
                'first_range_amount' => $chargeForFirstRange,
                'second_range_amount' => $chargeForSecondRange,
                'third_range_amount' => $chargeForThirdRange,
                'total_amount' => $totalAmount
            ];
        } else {
            return false;
        }
    }
}
