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
        Schema::create('maps', function (Blueprint $table) {
            $table->id();
            $table->string('address', 35);
            $table->foreignId('ledgerindex_id')->constrained('ledgerindexes')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->smallInteger('page')->default(1); //default first page
            $table->unsignedBigInteger('first')->nullable()->default(null); //inclusive SK * 10000
            $table->unsignedBigInteger('last')->nullable()->default(null);  //inclusive SK * 10000 (last evaluated)
            $table->string('condition',100);
            $table->unsignedTinyInteger('txtype'); //transactiontype
            $table->unsignedInteger('count_num');
            
            //$table->text('breakpoints');
            //$table->char('count_indicator',1);
            $table->timestamp('created_at');
            //$table->
            //$table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maps');
    }
};
