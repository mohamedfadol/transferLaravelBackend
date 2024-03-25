<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CurrencyRequest;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $currencies = Currency::get();
        if(is_null($currencies)) {
            return  ["success" => false, "message" => "There no currencys data"];
        }
        return $this->success([
            'currencies' => $currencies,
        ], 'Currency successfully.');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CurrencyRequest $request)
    {
        $data = $request->validated();
        try {
                $currency = new Currency();
                $currency->currency_name = $request->currency_name;
                $currency->currency_rate = $request->currency_rate;
                $currency->save();
            return  $this->success([
                'Currencys' => $currency],
                'Currency add successfully'
            );
           
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
