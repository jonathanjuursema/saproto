<?php

namespace Proto\Http\Controllers;

use Illuminate\Http\Request;

use Proto\Http\Requests;
use Proto\Http\Controllers\Controller;

use Proto\Models\OrderLine;
use Proto\Models\RfidCard;
use Proto\Models\Product;
use Proto\Models\User;
use Proto\Models\QrAuthRequest;

use Auth;
use Proto\Models\ProductCategory;

class OmNomController extends Controller
{
    public function display(Request $request, $store = null)
    {
        $stores = config('omnomcom.stores');

        if (array_key_exists($store, $stores)) {

            $storedata = $stores[$store];

            if (!in_array($request->ip(), $storedata->addresses) && (!Auth::check() || !Auth::user()->can($storedata->roles))) {
                abort(403);
            }

            $categories = [];

            foreach ($storedata->categories as $category) {
                $cat = ProductCategory::find($category);
                if ($cat) {
                    $prods = $cat->products();
                    $categories[] = (object)[
                        'category' => $cat,
                        'products' => $prods
                    ];
                }
            }

            if ($store == 'tipcie') {
                $minors = User::where('birthdate', '>', date('Y-m-d', strtotime('-18 years')))->has('member')->get();
            } else {
                $minors = collect([]);
            }

            return view('omnomcom.store.show', ['categories' => $categories, 'store' => $storedata, 'storeslug' => $store, 'minors' => $minors]);

        } else {
            return view('omnomcom.store.pick', ['stores' => $stores]);
        }
    }

    public function buy(Request $request, $store)
    {

        $stores = config('omnomcom.stores');

        if (array_key_exists($store, $stores)) {
            $storedata = $stores[$store];
            if (!in_array($request->ip(), $storedata->addresses) && !Auth::user()->can($storedata->roles)) {
                return "<span style='color: red;'>You are not authorized to do this.</span>";
            }
        } else {
            return "<span style='color: red;'>This store doesn't exist.</span>";
        }

        switch ($request->input('credentialtype')) {
            case 'card':
                $card = RfidCard::where('card_id', $request->input('credentials'))->first();
                if (!$card) {
                    return "<span style='color: red;'>Unknown card.</span>";
                }
                $card->touch();
                $user = $card->user;
                if (!$user) {
                    return "<span style='color: red;'>Unknown user.</span>";
                }
                break;

            case 'qr':
                $qrAuthRequest = QrAuthRequest::where('auth_token', $request->input('credentials'))->first();
                if (!$qrAuthRequest) {
                    return "<span style='color: red;'>Invalid authentication token.</span>";
                }

                $user = $qrAuthRequest->authUser();
                if (!$user) {
                    return "<span style='color: red;'>QR authentication hasn't been completed.</span>";
                }
                break;

            default:
                return "<span style='color: red;'>Invalid credential type.</span>";
                break;
        }

        if (!$user->member) {
            return "<span style='color: red;'>Only members can use the OmNomCom.</span>";
        }

        $withCash = $request->input('cash');

        if ($withCash == "true" && !$storedata->cash_allowed) {
            return "<span style='color: red;'>You cannot use cash in this store.</span>";
        }

        $cart = $request->input('cart');

        foreach ($cart as $id => $amount) {
            if ($amount > 0) {
                $product = Product::find($id);
                if (!$product) {
                    return "<span style='color: red;'>You tried to buy a product that didn't exist!</span>";
                }
                if (!$product->isVisible()) {
                    return "<span style='color: red;'>You tried to buy a product that is not available!</span>";
                }
                if ($product->stock < $amount) {
                    return "<span style='color: red;'>You tried to buy more of a product than was in stock!</span>";
                }
                if ($product->is_alcoholic && $user->age() < 18) {
                    return "<span style='color: red;'>You tried to buy alcohol, youngster!</span>";
                }

                if ($product->is_alcoholic && $stores[$store]->alcohol_time_constraint && !(date('Hi') > str_replace(':', '', config('omnomcom.alcohol-start')) || date('Hi') < str_replace(':', '', config('omnomcom.alcohol-end')))) {
                    return "<span style='color: red;'>You can't buy alcohol at the moment; alcohol can only be bought between " . config('omnomcom.alcohol-start') . " and " . config('omnomcom.alcohol-end') . ".</span>";
                }
            }
        }

        foreach ($cart as $id => $amount) {
            if ($amount > 0) {
                $product = Product::find($id);
                $product->buyForUser($user, $amount, $amount * $product->price, ($withCash == "true" ? true : false));
                if ($product->id == config('omnomcom.protube-skip')) {
                    file_get_contents(config('herbert.server') . "/skip?secret=" . config('herbert.secret'));
                }
            }
        }

        if ($user->show_omnomcom_total) {
            return sprintf("OK,TOTAL2,%s,%s", $user->calling_name, OrderLine::where('user_id', $user->id)->where('created_at', 'LIKE', sprintf("%s %%", date('Y-m-d')))->sum('total_price'));
        } else {
            return "OK";
        }

    }

    public function generateOrder(Request $request)
    {

        $products = Product::where('is_visible_when_no_stock', true)->whereRaw('stock < preferred_stock')->orderBy('name', 'ASC')->get();
        $orders = [];
        foreach ($products as $product) {
            $order_collo = ($product->supplier_collo > 0 ? ceil(($product->preferred_stock - $product->stock) / $product->supplier_collo) : 0);
            $order_products = $order_collo * $product->supplier_collo;
            $new_stock = $product->stock + $order_products;
            $new_surplus = $new_stock - $product->preferred_stock;

            $orders[] = (object)[
                'product' => $product,
                'order_collo' => $order_collo,
                'order_products' => $order_products,
                'new_stock' => $new_stock,
                'new_surplus' => $new_surplus
            ];
        }

        if ($request->has('csv')) {
            return view('omnomcom.products.generateorder_csv', ['orders' => $orders]);
        } else {
            return view('omnomcom.products.generateorder', ['orders' => $orders]);
        }
    }

    public function miniSite()
    {
        return view('omnomcom.minisite');
    }
}