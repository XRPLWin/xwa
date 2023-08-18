<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('issuer_id')->unsigned();
            $table->foreign('issuer_id')->references('id')->on('issuers')->onDelete('cascade');
            $table->string('currency',45);
            $table->string('amount');
            $table->integer('num_trustlines');
            $table->integer('num_holders');
            $table->integer('num_offers');
            $table->string('self_assessment_url')->nullable()->default(null);
            //$table->timestamps();

            $table->unique(['issuer_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tokens');
    }
};