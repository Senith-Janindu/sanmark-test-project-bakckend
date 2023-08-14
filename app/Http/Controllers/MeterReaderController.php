<?php

namespace App\Http\Controllers;

use App\Helper\ResponseHelper;
use App\Services\MeterReaderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeterReaderController extends Controller
{
    protected MeterReaderService $meterReaderService;

    protected ResponseHelper $responseHelper;

    public function __construct(MeterReaderService $meterReaderService, ResponseHelper $responseHelper)
    {
        $this->meterReaderService = $meterReaderService;
        $this->responseHelper = $responseHelper;
    }

    public function login(Request $request)
    {
        $loginStatus =  $this->meterReaderService->login($request->all());
        switch ($loginStatus['status']) {
            case true:
                return $this->responseHelper->response('success','Successfully logged in', $loginStatus['data'],Response::HTTP_OK);
            case false:
                return $this->responseHelper->response('failed','Unauthorized error', $loginStatus['data'],Response::HTTP_OK);
        }
    }

    public function addCustomerDetails (Request $request)
    {
        $customerDetailsStatus = $this->meterReaderService->addCustomerDetails($request->all());
        switch ($customerDetailsStatus['status']) {
            case true:
                return $this->responseHelper->response('success','Successfully added customer data', $customerDetailsStatus['data'],Response::HTTP_OK);
            case false:
                return $this->responseHelper->response('failed',$customerDetailsStatus['message'], $customerDetailsStatus['data'],Response::HTTP_OK);
        }
    }
}
