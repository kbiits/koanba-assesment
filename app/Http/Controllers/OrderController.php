<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    protected $orderRepo;
    protected $customerRepo;
    protected $productRepo;

    public function __construct(OrderRepository $orderRepo, CustomerRepository $customerRepo, ProductRepository $productRepo)
    {
        $this->orderRepo = $orderRepo;
        $this->customerRepo = $customerRepo;
        $this->productRepo = $productRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->query('itemsPerPage');

        $orders = $this->orderRepo->orderBy('created_at', 'DESC')->with(['customer', 'product'])->paginate($itemsPerPage);
        // dd($orders);
        return Inertia::render('Order', [
            'orders' => $orders->items(),
            'total' => $orders->total(),
            'currentPage' => $orders->currentPage(),
            'perPage' => (int) $orders->perPage(),
            'saveError' => session()->get('saveError'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();
        $customer = $this->customerRepo->getCustomerById($validated['customerId']);
        if (!$customer) {
            return redirect()->route('order')->with('saveError', 'Customer not found');
        }
        $validated['customerName'] = $customer->customerName;
        $product = $this->productRepo->getProductById($validated['productId']);
        if (!$product) {
            return redirect()->route('order')->with('saveError', 'Product not found');
        }
        $validated['productName'] = $product->productName;

        $orderDate = Carbon::parse($validated['orderDate'], 'Asia/Jakarta');
        $orderDate->setTimezone('Asia/Jakarta');
        $validated['orderDate'] = $orderDate;

        $order = $this->orderRepo->createOrder($validated);
        return redirect()->route('order');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validated = $request->validated();
        $orderDate = Carbon::parse($validated['orderDate'], 'Asia/Jakarta');
        $orderDate->setTimezone('Asia/Jakarta');
        $validated['orderDate'] = $orderDate;
        $validated['quality'] = (int) $validated['quality'];
        $customer = $this->customerRepo->getCustomerById($validated['customerId']);
        if (!$customer) {
            return redirect()->route('order')->with('saveError', 'Customer not found');
        }

        $product = $this->productRepo->getProductById($validated['productId']);
        if (!$product) {
            return redirect()->route('order')->with('saveError', 'Product not found');
        }
        $validated['productName'] = $product->productName;
        $validated['customerName'] = $customer->customerName;

        $order->fill($validated);
        $order->save();

        return redirect()->route('order');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Order $order)
    {
        $order->delete();
        return redirect()->to($request->get('redirectUrl'));
    }
}
