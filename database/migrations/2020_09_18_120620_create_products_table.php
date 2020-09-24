<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'products',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('inventoryAccountId')->nullable();
                $table->string('suppliersProductNo')->nullable();
                $table->boolean('isArchived')->default(false);
                $table->boolean('isInInventory')->default(false);
                $table->string('imageId')->nullable();
                $table->string('billy_product_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade');

                $table->timestamp('billy_created_at')->nullable();
                $table->timestamp('billy_updated_at')->nullable();
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
