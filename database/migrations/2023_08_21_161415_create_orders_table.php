<?php

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('orderId');
            $table->foreignIdFor(Customer::class, 'customerId');
            $table->string('customerName');
            $table->decimal('amount', 20, 2);
            $table->integer('quality');
            $table->foreignIdFor(Product::class, 'productId');
            $table->string('productName');
            $table->dateTime('orderDate')->default(DB::raw('now()'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
