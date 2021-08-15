<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagingCompaniesPhonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packaging_companies_phones', function (Blueprint $table) {
            $table->id();
//<<<<<<< HEAD
            $table->bigInteger('packaging_company_id')->unsigned();
            $table->foreign('packaging_company_id')->references('id')->on('packaging_companies')->onDelete('cascade');
//=======
//            $table->bigInteger('packagingCompanyId')->unsigned();
//            $table->foreign('packagingCompanyId')->references('id')->on('packaging_companies')->onDelete('cascade');
//>>>>>>> origin/osama
            $table->string('phoneNumber');
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
        Schema::dropIfExists('packaging_companies_phones');
    }
}
