<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('action_id');
            $table->integer('user_id');
            $table->double('coef');
            $table->double('sum');
            $table->double('leftover');
            $table->integer('move');
            $table->integer('team');
            $table->integer('related_bet')->nullable();
            $table->timestamps();
            /* ->nullable() */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bets');
    }
}
