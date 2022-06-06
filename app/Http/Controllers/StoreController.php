<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Store;
use Inertia\Inertia;
use Validator;

class StoreController extends Controller
{
  public function index()
  {
    // $data = Http::get("https://epood.ta19heinsoo.itmajakas.ee/api/hajus");
    // return $data;
    // $realData = array();
    return Inertia::render('Store');
  }
  public function cartList()
  {
    $data = session()->get('cart');
    $sendData = array();


    if($data){

        $stripe = new \Stripe\StripeClient(env('pk_test_51L6WxYCxL3Zhze4h1XnXO64MJ4oAjNRd8Az99ON6dWDO0DjwYiC2CrOxh1q2jHNN6KodFtfyQH7KFQBfojmQgOVH00VuguOGnX'));

        foreach ($data as $value) {

            $product = $stripe->products->create([
                'name' => $value["title"],
                'description' => $value["description"],
                'images' => ["https://epood.ta19heinsoo.itmajakas.ee/".$value["image"]],
                'default_price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int) $value["price"] * 100
                ],
                'metadata' => ['amount' => $value['qty']]
            ]);

            array_push($sendData, $product);
        }
    }

    return Inertia::render('Cart', [
        'data' => $data,
        'cart' => $sendData
    ]);

  }
  public function cartCount()
  {
    $data = session()->get('cart');
  }
  public function cartAdd(Request $request)
  {
    $product = $request->all();
    $validator = Validator::make($request->all(), [
      'qty' => 'required|numeric|gt:0'
    ])->passes();
    $qty = $validator ? $request->qty : 1;
    $product['qty'] = $qty;
    if (session()->has("cart." . $product['id'])) {
      $cart_product = session('cart.' . $product['id'], $product);
      // return $cart_product;
      $cart_product['qty'] += $qty;
      session()->put('cart.' . $product['id'], $cart_product);
    } else {
      session()->put('cart.' . $product['id'], $product);
    }
    return redirect()->back();
  }
  public function cartUpdate(Request $request)
  {
    $product = $request->all();
    $validator = Validator::make($request->all(), [
      'qty' => 'required|numeric|gt:0'
    ])->passes();
    if (session()->has('cart.' . $product['id']) && $validator) {
      $cart_product = session('cart.' . $product['id'], $product);
      $cart_product['qty'] += $request['qty'];
      session()->put('cart.' . $product['id'], $cart_product);
    }
    return redirect()->back();
  }
  public function destroy($id)
  {
    session()->forget('cart.' . $id);
    return redirect()->back();
  }

  function success(){
    session()->flush();

    return redirect('/store');
}

}
