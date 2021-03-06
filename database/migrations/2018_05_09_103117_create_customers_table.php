<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->dateTime('birthday')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->boolean('gender')->nullable();
            $table->unsignedInteger('living_city_id')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('kiotviet_id')->nullable();
            $table->timestamps();

            $table->foreign('living_city_id')->references('id')->on('cities');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
