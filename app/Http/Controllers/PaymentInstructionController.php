<?php

namespace App\Http\Controllers;

use App\Models\PaymentInstruction;
use Illuminate\Http\Request;

class PaymentInstructionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->jsonSuccess(200, "Payment Instructions", PaymentInstruction::all(), "paymentInstructions");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $paymentInstruction = PaymentInstruction::create($data);
        return $this->jsonSuccess(200, "Payment Instruction Created Successfully!!", $paymentInstruction, "paymentInstruction");

        return back()->with('message', 'Payment Instruction Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentInstruction  $paymentInstruction
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentInstruction $paymentInstruction)
    {
        return $this->jsonSuccess(200, "Payment Instruction", $paymentInstruction, "paymentInstruction");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentInstruction  $paymentInstruction
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentInstruction $paymentInstruction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PaymentInstruction  $paymentInstruction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentInstruction $paymentInstruction)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $paymentInstruction->update($data);
        return back()->with('message', 'Payment Instruction Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentInstruction  $paymentInstruction
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentInstruction $paymentInstruction)
    {
        $paymentInstruction->delete();
        return $this->jsonSuccess(200, "Payment Instruction Deleted Successfully", null, "paymentInstruction");

        return back()->with('message', 'Payment Instruction Deleted Successfully');
    }
}
