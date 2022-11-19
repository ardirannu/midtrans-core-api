<?php

namespace App\Http\Controllers\API\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function post(Request $request)
    {
        try {
            $notification_body = json_decode($request->getContent(), true);
            //order id, transaction id dan status code ini adalah object yang dikirim oleh notification midtrans, jadi parameternya harus sama
            //nomor invoice adalah order id yang akan ditampilkan pada interface pengguna
            $order_code = $notification_body['order_id'];
            //transaction id adalah kode transaksi yang bersifat secret, jangan ditampilkan ke interface
            $transaction_code = $notification_body['transaction_id'];
            $status_code = $notification_body['status_code'];

            //input status pembayaran ke database dengan mencocokan 2 parameter sensitif order id dan transaction id
            $order = Order::where('order_code', $order_code)->where('transaction_code', $transaction_code)->firstOrFail();

            if(!$order){
                return ['code' => 0, 'message' => 'Terjadi kesalahan | Pesanan tidak ditemukan'];
            }else{
                switch($status_code){
                    case '200':
                        $order->status = 'SUCCESS';
                        break;
                    case '201':
                        $order->status = 'PENDING';
                        break;
                    case '202':
                        $order->status = 'CANCEL';
                        break;
                }
                
                $order->save();

                //return response push notification ke Midtrans menggunakan response header
                //response header untuk mengirim response ke sistem yg tidak mengenali object dari response api kita
                //jadi mereka membaca langsung response headernya
                return response('Ok', 200)->header('Content-Type', 'text/plain');
            }
        } catch (\Exception $e) {
            dd($e);
                return response('Error', 404)->header('Content-Type', 'text/plain');
        }
    }
}
