<?php

use App\Models\Customer;
use App\Models\Route;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('route_customer', function (Blueprint $table) {
            $table->foreignIdFor(Route::class);
            $table->foreignIdFor(Customer::class);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_customer');
    }
};
