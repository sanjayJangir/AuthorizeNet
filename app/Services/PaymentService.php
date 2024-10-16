<?php

namespace App\Services;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentService
{
    public function makePayment($cardNumber, $expirationDate, $cvv, $amount)
    {
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('AUTHORIZE_NET_API_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('AUTHORIZE_NET_TRANSACTION_KEY'));

        $refId = 'ref' . time();

        // Create the payment data
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expirationDate);
        $creditCard->setCardCode($cvv);

        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create a transaction request
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setPayment($paymentOne);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(env('AUTHORIZE_NET_MODE') === 'live' ? \net\authorize\api\constants\ANetEnvironment::PRODUCTION : \net\authorize\api\constants\ANetEnvironment::SANDBOX);

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    return [
                        'status' => 'success',
                        'transaction_id' => $tresponse->getTransId(),
                        'message' => $tresponse->getMessages()[0]->getDescription(),
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'Transaction Failed',
                    ];
                }
            } else {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return [
                        'status' => 'failed',
                        'message' => $tresponse->getErrors()[0]->getErrorText(),
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'Transaction Failed',
                    ];
                }
            }
        } else {
            return [
                'status' => 'failed',
                'message' => 'No response returned',
            ];
        }
    }
}
