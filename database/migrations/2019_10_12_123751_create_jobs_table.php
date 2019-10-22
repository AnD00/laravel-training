<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'jobs',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100);
                $table->string('category', 20);
                $table->string('detail', 2000);
                $table->unsignedInteger('company_id');
                $table->timestamps();

                $table->index('company_id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
