<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePanicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panics', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at')->nullable();
            $table->string('longitude', 255);
            $table->string('latitude', 255);
            $table->string('panic_type', 255)->nullable();
            $table->string('details', 255)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('wayne_id')->nullable();
            $table->bigInteger('deleted')->default(0);
            $table->bigInteger('user_delete')->default(0);
            $table->foreign('user_id')->references('id')->on('users'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('panics');
    }
}
