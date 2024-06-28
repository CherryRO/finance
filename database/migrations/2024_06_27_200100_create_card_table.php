<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('bank');
            $table->string('cardholder_name');
            $table->string('first_four_digits', 4);
            $table->string('last_four_digits', 4);
            $table->string('expiry_date');
            $table->enum('status', ['1', '2', '3', '4', '5'])->default('1'); // 1 - Activ, 2 - Inactiv, 3 - Sters, 4 - Expirat, 5 - Pierdut/Furat
            $table->text('observatii')->nullable();
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
        Schema::dropIfExists('cards');
    }
}
