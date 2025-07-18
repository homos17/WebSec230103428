<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller {
    use ValidatesRequests;

    public function ___construct()
    {
        $this->middleware('auth:web')->except('list');
    }
    public function buy(Request $request, Product $product) {
        $user = Auth::user();

        if (!Auth()-> user() ) return redirect('login');

        $quantity = $request->input('quantity', 1);
        $totalPrice = $product->price * $quantity;

        if ($user->balance < $totalPrice) {
            return redirect()->route('insufficient_balance');
        }

        $user->balance -= $totalPrice;
        $user->save();
        $product ->stock -= $quantity;
        $product ->save();

        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
            'created_at' => now(),
        ]);

        return redirect()->route('invoice', ['order' => $order->id]);
    }

    public function invoice(Order $order) {
        return view('products.invoice', compact('order'));
    }

    public function insufficientBalance() {
        return view('products.insufficient_balance');
    }


public function list(Request $request) {
    $query = Product::select("products.*");
    $query->when($request->keywords,
    fn($q)=> $q->where("name","like","%$request->keywords%"));
    $query->when($request->min_price,
        fn($q)=> $q->where("price", ">=", $request->min_price));
    $query->when($request->max_price,
        fn($q)=> $q->where("price", "<=", $request->max_price));
    $query->when($request->order_by,
    fn($q)=> $q->orderBy($request->order_by, $request->order_direction??"ASC"));
    $products = $query->get();
    return view("products.list", compact('products'));

}
public function edit(Request $request, Product $product = null) {
    if(!auth()->user()) return redirect('login');
    $product = $product??new Product();
    return view("products.edit", compact('product'));
    }

    public function save(Request $request, Product $product = null) {

        $this->validate($request, [
	        'code' => ['required', 'string', 'max:32'],
	        'name' => ['required', 'string', 'max:128'],
	        'model' => ['required', 'string', 'max:256'],
	        'description' => ['required', 'string', 'max:1024'],
	        'price' => ['required', 'numeric'],
            'stock' => ['required','integer','min:0']
	    ]);


		$product = $product??new Product();
		$product->fill($request->all());
		$product->save();

        return redirect()->route('products_list');
}


public function delete(Request $request, Product $product) {
    if(!auth()->user()) return redirect('login');
        $product->delete();
        return redirect()->route('products_list');
    }



    public function review(Request $request, Product $product) {
        if (!auth()->user()) return redirect('login');
        return view('products.review', compact('product'));
    }

    public function saveReview(Request $request, Product $product) {
        if (!auth()->user()) return redirect('login');

        $this->validate($request, [
            'review' => ['required', 'string', 'max:1024']
        ]);

        $product->review = $request->input('review');
        $product->save();

        return redirect()->route('products_list')->with('success', 'Review submitted successfully.');
    }



}
