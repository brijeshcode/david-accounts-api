<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            
            $table->string('name', 150);
            $table->string('email', 200)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 200)->nullable();
            $table->boolean('on_invoice')->default(false);
            
            $table->text('note')->nullable()->comment('additional information for this entry');
            $table->boolean('is_active')->default(true);

            $table->unsignedBigInteger('created_by_id')->default('1')->comment('user who added this enty');
            $table->ipAddress('created_by_ip')->nullable();
            $table->string('created_by_agent', 1023)->nullable();

            $table->softDeletes();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
