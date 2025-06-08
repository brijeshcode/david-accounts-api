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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('starting_balance', 30, 11)->default(0);
            $table->decimal('balance', 30, 11)->default(0);
            $table->string('address')->nullable();
            $table->string('account_no', 100)->nullable();

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
        Schema::dropIfExists('banks');
    }
};
