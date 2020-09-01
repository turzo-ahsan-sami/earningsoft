<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code');
            $table->float('price', 8, 2);
            // $table->json('modules');
            $table->smallInteger('planId');
            $table->mediumInteger('numberOfUser');
            $table->json('features')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('module_product',function(Blueprint $table)
        {
            // $table->bigIncrements('id');
            $table->integer('module_id');
            $table->integer('product_id');
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
        Schema::dropIfExists('products');
        Schema::dropIfExists('module_product');
    }

}
