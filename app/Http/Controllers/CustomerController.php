<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CustomerController extends Controller
{

    /**
     * @var CustomerRepository $customerRepo
     */
    protected $customerRepo;

    public function __construct(CustomerRepository $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->query('itemsPerPage');

        $customers = $this->customerRepo->orderBy('created_at', 'DESC')->paginate($itemsPerPage);
        return Inertia::render('Customer', [
            'customers' => $customers->items(),
            'total' => $customers->total(),
            'currentPage' => $customers->currentPage(),
            'perPage' => (int) $customers->perPage(),
        ]);
    }

    public function loadCustomersCursorBased(Request $request)
    {
        $cursor = $request->query('cursor');
        $search = $request->query('search');
        $query = Customer::orderBy('customerId', 'ASC')->limit(10)->where('customerId', '>', $cursor);
        if (!empty($search)) {
            $query = $query->where('customerName', 'LIKE', sprintf("%%%s%%", $search));
        }

        $customers = $query->get();
        $respData = [
            'customers' => $customers,
        ];
        if ($customers->count() > 0) {
            $respData['lastId'] = $customers->last()->customerId;
        }
        return response()->json($respData);
    }

    public function show($customerId)
    {
        $customer = $this->customerRepo->getCustomerById($customerId);
        if (!$customer) {
            return redirect()->route('not_found');
        }
        return Inertia::render('CustomerDetail', [
            'customer' => $customer,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();
        $this->customerRepo->addCustomer($validated);
        return redirect()->route('customer');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();
        $this->customerRepo->editCustomer($validated, $customer);
        return redirect()->to($request->get('redirectUrl'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Customer $customer)
    {
        $customer->delete();
        return redirect()->to($request->get('redirectUrl'));
    }
}
