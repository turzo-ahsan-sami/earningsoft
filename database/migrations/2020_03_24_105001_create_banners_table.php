<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('banner_text1')->nullable();
            $table->longText('banner_text2')->nullable();
            $table->longText('banner_text3')->nullable();
            $table->longText('button_text1')->nullable();
            $table->longText('button_text2')->nullable();
            $table->Text('banner_image')->nullable();
            $table->Text('mini_image')->nullable();
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
        Schema::dropIfExists('banners');
    }
}
