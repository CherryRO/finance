<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('requests', function (Blueprint $table) {
      $table->id();
      $table->string('full_name');
      $table->string('contact_number');
      $table->enum('priority', ['1', '2', '3'])->default('1');
      $table->decimal('suma_necesara', 8, 2);
      $table->foreignId('center_of_cost_id')->constrained('centers_of_cost')->onDelete('cascade');
      $table->string('detalii_plata');
      $table->string('payment_method')->default('card');
      $table->foreignId('card')->constrained('cards')->onDelete('cascade');
      $table->enum('status', ['1', '2', '3'])->default('1'); // 1 - Created, 2 - Acceptat, 3 - Respins
      $table->text('observatii_financiar')->nullable();
      $table->text('observatii_solicitant')->nullable();
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
    Schema::dropIfExists('requests');
  }
}
