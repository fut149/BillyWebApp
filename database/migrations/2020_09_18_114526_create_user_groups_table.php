<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGroupsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'user_groups',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('name');
                $table->string('natureId')->default('expense');
                $table->integer('priority')->default(0);
                $table->string('billy_gorup_id')->nullable();
                $table->timestamp('billy_created_at')->nullable();
                $table->timestamp('billy_updated_at')->nullable();
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('user_group');
    }
}
