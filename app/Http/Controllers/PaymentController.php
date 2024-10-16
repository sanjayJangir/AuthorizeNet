<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;


class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('new');
    }

    public function processPayment(Request $request)
    {
        $validatedData = $request->validate([
            'card_number' => 'required',
            'expiration_date' => 'required',
            'cvv' => 'required',
            'amount' => 'required|numeric',
        ]);

        $paymentResponse = $this->paymentService->makePayment(
            $validatedData['card_number'],
            $validatedData['expiration_date'],
            $validatedData['cvv'],
            $validatedData['amount']
        );

        return response()->json($paymentResponse);
    }
}
