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
        Schema::create('t_sales_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('t_sales_id')
                ->comment('Fill with id from table t_sales');
            $table->string('m_product_id')
                ->comment('Fill with id from table m_product');
            $table->string('m_product_detail_id')
                ->comment('Fill with id from table m_product_detail');
            $table->integer('total_item')
                ->comment('Fill total_item of sales detail');
            $table->double('price')
                ->comment('Fill price of sales detail');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->integer('deleted_by')->default(0);

            $table->index('t_sales_id');
            $table->index('m_product_id');
            $table->index('m_product_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_sales_detail');
    }
};
