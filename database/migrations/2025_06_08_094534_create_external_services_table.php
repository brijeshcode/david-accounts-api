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
        Schema::create('external_services', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_id')->default('1')->comment('user who added this enty');
            $table->ipAddress('created_by_ip')->nullable();
            $table->string('created_by_agent',1023)->nullable();


            $table->softDeletes();
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
        Schema::dropIfExists('external_services');
    }
};
