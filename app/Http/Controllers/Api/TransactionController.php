<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\AccountTransaction;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
       $user_id =  $request->user()->id;
        $tansactions = Transaction::where('created_by', $user_id)->get();
        if(is_null($tansactions)) {
            return  ["success" => false, "message" => "There no transaction data"];
        }
        return $this->success([
            'tansactions' => $tansactions,
        ], 'Transaction successfully.');
    }


    public function generateReferenceNumberTransaction()
    {
        return Uuid::uuid4()->toString();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request)
    {
        $data = $request->validated();
        try {
                DB::beginTransaction();
                
                $user = $request->user();
                $transaction = new Transaction();
                $transaction->status = $request->status;
                $transaction->created_by = $user->id;
                $transaction->ref_no = $this->generateReferenceNumberTransaction();
                $transaction->final_total = $request->final_total;
                $transaction->save();
                $transaction_payment = TransactionPayment::create(['transaction_id' => $transaction->id, 'amount' => $request->final_total, 'method' => $request->method]);
                $deposit_to = [
                    'account_id' => $request->account_deposit,
                    'transaction_id' => $transaction->id,
                    'transaction_payment_id' => $transaction_payment->id,
                    'amount' => $request->final_total,
                    'type' => 'debit',
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id
                ];

                $account_from =[
                    'account_id' => $request->account_withdraw,
                    'transaction_id' => $transaction->id,
                    'transaction_payment_id' => $transaction_payment->id,
                    'amount' => $request->final_total,
                    'type' => 'credit',
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id
                ];

                AccountTransaction::create($deposit_to);
                AccountTransaction::create($account_from);

                DB::commit();

            return  $this->success([
                'transactions' => $transaction->load('payment.acconttransactions.account.owner')],
                'Transaction add successfully'
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
    public function getAllUserTransaction(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $user = $request->user();
            $transactions =  Transaction::with('payment.acconttransactions.account')->get();
         
            DB::commit();
        return  $this->success([
            'transactions' => $transactions],
            'Transaction add successfully'
        );
       
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
        return $this->error($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
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
