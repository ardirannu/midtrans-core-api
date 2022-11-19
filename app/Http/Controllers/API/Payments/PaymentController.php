<?php

namespace App\Http\Controllers\API\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//substitution midtrans Core API
use App\Http\Controllers\Midtrans\Config;
use App\Http\Controllers\Midtrans\CoreApi;
use App\Models\Order;
use PhpParser\Node\Stmt\Break_;

class PaymentController extends Controller
{
    public function buyProduct(Request $request)
    {
        try {
            $result = null;
            $payment_method = $request->payment_method;
            $order_id = "#RQDTSDQY21319";
            $total_amount = 10000;

            //jumlah gross amount harus sama dengan total harga item details
            $transaction = array(
                "transaction_details" => [
                    "gross_amount" => 10000,
                    "order_id" => "#RQDTSDQY21319",
                ],
                "customer_details" => [
                    "email" => "rannuardianto77@gmail.com",
                    "first_name" => "Ardianto",
                    "last_name" => "Rannu",
                    "phone" => "+6285824103510",
                ],
                "item_details" => array([
                    "id" => "1388998298204",
                    "price" => 5000,
                    "quantity" => 1,
                    "name" => "Ayam Kentucky"
                ], [
                    "id" => "1388998298205",
                    "price" => 5000,
                    "quantity" => 1,
                    "name" => "Ayam Goreng"
                ]), 
            );

            switch($payment_method){
                case 'bank_transfer':
                    //call function chargeBankTransfer
                    $result = self::chargeBankTransfer($order_id, $total_amount, $transaction);
                    break;
                case 'e-wallet':
                    //call function EWallet
                    // $result = self::chargeEWallet($order_id, $total_amount, $transaction);
                    break;
            }
            return $result;
        } catch (\Exception $e) {
            dd($e);
            return ['code' => 0, 'message' => 'Terjadi kesalahan'];
        }
    }

    //menggunakan static function karna dipanggil menggunakan self, bukan $this
    static function chargeBankTransfer($order_id, $total_amount, $transaction_object)
    {
        try {
            $transaction = $transaction_object;
            $transaction['payment_type'] = 'bank_transfer';
            $transaction['bank_transfer'] = [
                "bank" => "bca",
                "va_number" => "111111",
            ];

            $charge = CoreApi::charge($transaction);
            if(!$charge){
                return ['code' => 0, 'message' => 'Terjadi kesalahan'];
            }

            //simpan data pembayaran ke table order
            $order = new Order();
            $order->order_code = $order_id;
            $order->transaction_code = $charge->transaction_id;
            $order->status = "PENDING";
            $order->total_harga = $total_amount;

            if(!$order->save()){
                return false;
            }
            return ['code' => 1, 'message' => 'Success', 'result' => $charge];
        } catch (\Exception $e) {
            dd($e);
            return ['code' => 0, 'message' => 'Terjadi kesalahan'];
        }

    }
}
