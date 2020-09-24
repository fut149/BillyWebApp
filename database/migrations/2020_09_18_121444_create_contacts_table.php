<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'contacts',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type')->default('company');
                $table->string('name');
                $table->string('countryId')->default('BG');
                $table->string('street')->nullable();
                $table->string('cityText')->nullable();
                $table->string('stateText')->nullable();
                $table->string('zipcodeText')->nullable();
                $table->string('phone')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade');

                $table->string('billy_contact_id')->nullable();
                $table->timestamp('billy_created_at')->nullable();
                $table->timestamp('billy_updated_at')->nullable();
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
