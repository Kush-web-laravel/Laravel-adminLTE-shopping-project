<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Payment;
use Mpdf\Mpdf;
use App\Mail\PaymentInvoice;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\CartItem;
use App\Notifications\CartNotification;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class ProductController extends Controller
{
    //
    public function index()
    {
        return view('products');
        
    }

    public function addProduct()
    {
        return view('add-products');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'productName' => 'required',
            'productPrice' => 'required',
            'productDetails' => 'required',
            'productImage' => 'required|mimes:jpg,jpeg,png,gif'
        ]);
    
        $filePath = null;
    
        if ($request->hasFile('productImage')) {
            $file = $request->file('productImage');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = public_path('uploads/product/' . $fileName);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);
            $image->toJpeg(80)->save($path);
            $filePath = 'uploads/product/' . $fileName;
        }
    
       
        Product::create([
            'user_id' => auth()->id(),
            'productName' => $request->productName,
            'productPrice' => $request->productPrice,
            'productDetails' => $request->productDetails,
            'productImage' => $filePath,
        ]);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'redirect_url' => route('products.index')
        ]);
    }
    

    public function showProducts()
    {
        $products = Product::where('user_id', auth()->id())->get();
        $cartItems = CartItem::where('user_id', auth()->id())->pluck('product_id')->toArray();

        return DataTables::of($products)
            ->addColumn('in_cart', function($product) use ($cartItems) {
                return in_array($product->id, $cartItems);
            })
            ->addColumn('actions', function($product) use ($cartItems) {
                $inCart = in_array($product->id, $cartItems);
                
                $cartButton = $inCart ? '' : '<button type="button" class="btn btn-sm btn-light cart-btn" data-id="' . $product->id . '">Add to Cart</button>';

                return '
                    <a href="/edit-products/' . $product->id . '" class="btn btn-sm btn-success">Edit</a>
                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $product->id . '">Delete</button>
                    ' . $cartButton . '
                    <button type="button" class="btn btn-sm btn-warning pay-btn" data-id="' . $product->id . '">Buy now</button>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }


    

    public function  editProduct($id)
    {
        $product = Product::findOrFail($id);
        return view('edit-products', compact('product'));
    }

    public function updateProduct(Request $request)
    {

        $product = Product::find($request->id);

        $request->validate([
            'productName' => 'required',
            'productPrice' => 'required',
            'productDetails' => 'required',
            'productImage' => 'mimes:jpg,jpeg,png,gif'
        ]);
    
        if($request->hasFile('productImage')){
            $file = $request->file('productImage');
            $fileName = time().''.$file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/profile_images', $fileName , 'public');
        }else{
            $filePath = $request->input('existing_productImage');
        }

        $product->update([
            'productName' => $request->productName,
            'productPrice' => $request->productPrice,
            'productDetails' =>  $request->productDetails,
            'productImage' => $filePath,
        ]);

    
        return response()->json(['status' => 'success', 'redirect_url' => route('products.index')]);
    }
    public function deleteProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if($product){
            $product->delete();
            
            return response()->json([
                'status' => 'success',    
                'message' => 'Product deleted successfully'
            ]);
        }else{
            return response()->json([
                'status' => 'error',    
                'message' => 'Failed to delete product'
            ]);
        }
    }

    public function addToCart($id)
    {
        $product = Product::find($id);

   
        $cartItem = CartItem::where('user_id', auth()->id())
                            ->where('product_id', $product->id)
                            ->first();

        if ($cartItem) {
            
            $cartItem->product_quantity += 1;
            $cartItem->save();
        } else {
          
            $cartItem = new CartItem();
            $cartItem->user_id = auth()->id();
            $cartItem->product_id = $product->id;
            $cartItem->product_name = $product->productName;
            $cartItem->product_price = $product->productPrice;
            $cartItem->product_image = $product->productImage;
            $cartItem->product_quantity = 1;
            $cartItem->save();
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }


    
    public function showCart()
    {
        $cartItems = CartItem::where('user_id', auth()->id())->get();
        return view('cart-product', compact('cartItems'));
    }

    public function removeFromCart($id)
    {
       $cartItem = CartItem::find($id);

        if($cartItem){
            $cartItem->delete();
            
            return response()->json([
                'success' => true,    
                'redirect_url' => route('cart.index')
            ]);
        }

        return response()->json(['success' => false]);

    }


    public function buyProduct($id)
    {
        $product =  Product::find($id);
            
        return view('buy-product', compact('product'));
        
    }

    public function makePayment(Request $request)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $response = $stripe->checkout->sessions->create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'inr',
                        'product_data' => [
                            'name' => $request->product_name,
                        ],
                        'unit_amount' => $request->price * 100,
                    ],
                    'quantity' => $request->quantity,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('product.successfullPayment') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('product.cancelledPayment'),
            'metadata' => [
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
            ],
        ]);
    
        if (isset($response->id) && $response->id != '') {
            session()->put('user_id', $request->user_id);
            session()->put('product_id', $request->product_id);
            session()->put('quantity', $request->quantity);
            session()->put('price', $request->price);
            return redirect($response->url);
        } else {
            return redirect()->route('product.cancelledPayment');
        }
    }
    

    public function successfullPayment(Request $request)
    {
        if (isset($request->session_id)) {

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);

        
            $payment = new Payment();
            $payment->user_id = $response->metadata->user_id;
            $payment->product_id = $response->metadata->product_id;
            $payment->quantity = session()->get('quantity');
            $payment->amount = session()->get('price');
            $payment->total_amount = session()->get('price') * session()->get('quantity');
            $payment->currency = $response->currency;
            $payment->payment_status = $response->status;
            $payment->payment_method = "Stripe";
            $payment->stripe_payment_id = $response->payment_intent;

            $paymentDetails = [
                'user_id' => $payment->user_id,
                'product_id' => $payment->product_id,
                'product_name' => Product::find($payment->product_id)->productName,
                'quantity' => $payment->quantity,
                'amount' => $payment->amount,
                'total_amount' => $payment->total_amount,
                'currency' => $payment->currency,
                'payment_status' => $payment->payment_status,
                'payment_method' => $payment->payment_method,
                'stripe_payment_id' => $payment->stripe_payment_id
            ];
    
            $payment->payment_details = json_encode($paymentDetails);
            $payment->save();

    
            $payments = Payment::where('user_id', auth()->id())->with('product')->get();

            $html = view('payment-bill', $payments)->render();

            
            $mpdf = new Mpdf();
            $mpdf->WriteHTML($html);

    
            $pdf = $mpdf->Output('', 'S'); 

        
            $user = User::find($payment->user_id); 
            Mail::to($user->email)->send(new PaymentInvoice($payments, $pdf));

            return redirect()->route('products.myOrders')->with('success', 'Payment is successful ane email has been sent with the invoice');
            
            session()->forget('user_id');
            session()->forget('product_id');
            session()->forget('quantity');
            session()->forget('price');

        } else {
            return redirect()->route('product.cancelledPayment');
        }
    }


    public function cancelledPayment(Request $request)
    {
        $payment = new Payment();
        $payment->user_id = session()->get('user_id');
        $payment->product_id = session()->get('product_id');
        $payment->quantity = session()->get('quantity');
        $payment->amount = session()->get('price');
        $payment->total_amount = session()->get('price') * session()->get('quantity');
        $payment->currency = 'inr'; 
        $payment->payment_status = 'cancelled';
        $payment->payment_method = "Stripe";

        $paymentDetails = [
            'user_id' => $payment->user_id,
            'product_id' => $payment->product_id,
            'product_name' => Product::find($payment->product_id)->productName,
            'quantity' => $payment->quantity,
            'amount' => $payment->amount,
            'total_amount' => $payment->total_amount,
            'currency' => $payment->currency,
            'payment_status' => $payment->payment_status,
            'payment_method' => $payment->payment_method,
        ];

        $payment->payment_details = json_encode($paymentDetails);
        $payment->save();

        session()->forget('user_id');
        session()->forget('product_id');
        session()->forget('quantity');
        session()->forget('price');

        return redirect()->route('products.myOrders')->with('error', 'Sorry, the process has been cancelled. Please try again.');
    }

        
    public function myOrders()
    {
        $payments = Payment::where('user_id', auth()->id())->with('product')->get();
    
        return view('my-orders', compact('payments'));
    }
        

    
    public function refund(Request $request, $paymentId)
    {
       
        $payment = Payment::find($paymentId);

        if ($payment->payment_status == 'refunded') {
            return redirect()->back()->with('error', 'This item has already been refunded.');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        try {
           
            $refundAmount = $payment->amount * 100; 

            $refund = $stripe->refunds->create([
                'payment_intent' => $payment->stripe_payment_id,
                'amount' => $refundAmount,
            ]);

            
            $payment->payment_status = 'refunded';
            $payment->save();

            return redirect()->back()->with('success', 'The item has been successfully refunded.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while processing the refund: ' . $e->getMessage());
        }
    }

    public function updateQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Validate the quantity
        if ($quantity <= 0 || $quantity > 1000) {
            return response()->json(['success' => false, 'message' => 'Invalid quantity']);
        }

        // Update the quantity in the database
        $cartItem = CartItem::where('user_id', auth()->id())->where('product_id', $productId)->first();
        if ($cartItem) {
            $cartItem->product_quantity = $quantity;
            $cartItem->save();
            return response()->json(['success' => true, 'message' => 'Quantity updated']);
        }

        return response()->json(['success' => false, 'message' => 'Product not found in cart']);
    }

    public function cartPayment(Request $request)
    {
        $stripe  = new \Stripe\StripeClient(config('services.stripe.secret'));

        $cartItems = CartItem::where('user_id', $request->user_id)->get();
        $lineItems = [];
        $discount = $request->discount;
        $discountRate = 0;
        if($discount == 'GET20'){
            $discountRate = 20;
        }
        foreach($cartItems as $item){
            $price = $item->product_price;
            //dd($price);
            if($discountRate > 0){
                $price = $price - ($price * $discountRate / 100);
            }
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'inr',
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => $price * 100,
                ],
                'quantity' => (int)$item->product_quantity,
            ];
            
        }

        $metadata = [
            'user_id' => $request->user_id,
        ];

        foreach ($cartItems as $item) {
            $metadata['product_id'] = $item->product_id;
        }

        $response = $stripe->checkout->sessions->create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('product.cart.successfullPayment') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('product.cart.cancelledPayment'),
            'metadata' => $metadata,
        ]);

        if (isset($response->id) && $response->id != '') {
            session()->put('user_id', $request->user_id);
            $products = [];
            foreach ($cartItems as $item) {
                $products[] = [
                    'product_id' =>$item->product_id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->product_price,
                    'product_quantity' => $item->product_quantity,
                ];
            }
            //dd($products);
            session()->put('products', $products);
            session()->put('discount', $request->discount);
            return redirect($response->url);
        } else {
            return redirect()->route('product.cart.cancelledPayment');
        }
    }

    public function cartSuccessfulPayment(Request $request)
    {
        if (isset($request->session_id)) {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);
    
            $user_id = $response->metadata->user_id;
            $products = session()->get('products', []);
            $disc = session()->get('discount', '');
    
            $discountRate = 0;
            if ($disc == 'GET20') {
                $discountRate = 20;
            }
    
            foreach ($products as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['product_quantity'];
                $price = $item['product_price'];
                $discountedPrice = $price - ($price * $discountRate / 100);
                $total_amount = $discountedPrice * $quantity;
    
                $payment = new Payment();
                $payment->user_id = $user_id;
                $payment->product_id = $product_id;
                $payment->quantity = $quantity;
                $payment->amount = $discountedPrice;
                $payment->total_amount = $total_amount;
                $payment->currency = $response->currency;
                $payment->payment_method = 'stripe';
                $payment->payment_status = $response->status;
                $payment->stripe_payment_id = $response->payment_intent;
    
                
                $product = Product::find($payment->product_id);
                $paymentDetails = [
                    'user_id' => $payment->user_id,
                    'product_id' => $payment->product_id,
                    'product_name' => $product ? $product->productName : 'Unknown Product',
                    'quantity' => $payment->quantity,
                    'amount' => $payment->amount,
                    'total_amount' => $payment->total_amount,
                    'currency' => $payment->currency,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                    'stripe_payment_id' => $payment->stripe_payment_id,
                ];
    
                $payment->payment_details = json_encode($paymentDetails);
                $payment->save();
            }
    
            $payments = Payment::where('user_id', auth()->id())
                        ->where('stripe_payment_id', $payment->stripe_payment_id)->with('product')->get();
            //dd($payments);
            $html = view('payment-bill', compact('payments'))->render();
    
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);
            $pdf = $mpdf->Output('', 'S'); 
    
            $user = User::find($payment->user_id); 
            Mail::to($user->email)->send(new PaymentInvoice($payments, $pdf));
    
            session()->forget(['user_id', 'products', 'discount', 'stripe_session_id']);
    
            return redirect()->route('products.myOrders')->with('success', 'Payment is successful and an email has been sent with the invoice');
        } else {
            return redirect()->route('product.cancelledPayment')->with('error', 'Payment failed');
        }
    }
    


    public function cartCancelledPayment(Request $request)
    {
        $user_id = session()->get('user_id');
        $products = session()->get('products', []);
        $currency = 'inr';
        $payment_method = 'Stripe';
        $payment_status = 'cancelled';
    
        foreach ($products as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['product_quantity'];
            $price = $item['product_price'];
            $total_amount = $price * $quantity;
    
            $payment = new Payment();
            $payment->user_id = $user_id;
            $payment->product_id = $product_id;
            $payment->quantity = $quantity;
            $payment->amount = $price;
            $payment->total_amount = $total_amount;
            $payment->currency = $currency;
            $payment->payment_status = $payment_status;
            $payment->payment_method = $payment_method;
    
            $paymentDetails = [
                'user_id' => $payment->user_id,
                'product_id' => $payment->product_id,
                'product_name' => Product::find($payment->product_id)->name,
                'quantity' => $payment->quantity,
                'amount' => $payment->amount,
                'total_amount' => $payment->total_amount,
                'currency' => $payment->currency,
                'payment_status' => $payment->payment_status,
                'payment_method' => $payment->payment_method,
            ];
    
            $payment->payment_details = json_encode($paymentDetails);
            $payment->save();
        }
    
        session()->forget('user_id');
        session()->forget('products');
        session()->forget('discount');
        session()->forget('stripe_session_id');
    
        return redirect()->route('products.myOrders')->with('error', 'Sorry, the process has been cancelled. Please try again.');
    }
    
    public function notifyUser(Request $request)
    {

        $user = User::find(1);
  
        $messages["hi"] = "Hey, {$user->name}";
        $messages["notification"] = "There are few items in your cart. We would like you to have look at them.";
          
        $user->notify(new CartNotification($messages));
  
        dd('Done');
    }


}