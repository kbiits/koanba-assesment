<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * @var ProductRepository $productRepo
     */
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
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


        $products = $this->productRepo->orderBy('created_at', 'DESC')->paginate($itemsPerPage);
        return Inertia::render('Product', [
            'products' => $products->items(),
            'total' => $products->total(),
            'currentPage' => $products->currentPage(),
            'perPage' => (int) $products->perPage(),
        ]);
    }

    public function loadProductsCursorBased(Request $request)
    {
        $cursor = $request->query('cursor');
        $search = $request->query('search');
        $query = Product::orderBy('productId', 'ASC')->limit(10)->where('productId', '>', $cursor);
        if (!empty($search)) {
            $query = $query->where('productName', 'LIKE', sprintf("%%%s%%", $search));
        }

        $products = $query->get();
        $respData = [
            'products' => $products,
        ];
        if ($products->count() > 0) {
            $respData['lastId'] = $products->last()->productId;
        }
        return response()->json($respData);
    }

    public function show($productId)
    {
        $product = $this->productRepo->getProductById($productId);
        if (!$product) {
            return redirect()->route('not_found');
        }
        return Inertia::render('ProductDetail', [
            'product' => $product,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productRepo->addProduct($request->validated());
        return redirect()->route('product');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productRepo->editProduct($request->validated(), $product);
        return redirect()->to($request->get('redirectUrl'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Product $product)
    {
        //
        $product->delete();
        return redirect()->to($request->get('redirectUrl'));
    }
}
