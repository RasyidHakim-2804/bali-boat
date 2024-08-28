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
        Schema::create('xendit_payout_links', function (Blueprint $table) {
            $table->id();
            $table->string('payout_link_id');
            $table->string('reference_id');
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->text('failure_reason')->nullable();
            $table->string('type');
            $table->string('payout_url');
            $table->string('bank_code')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
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
        Schema::dropIfExists('xendit_payout_links');
    }
};
