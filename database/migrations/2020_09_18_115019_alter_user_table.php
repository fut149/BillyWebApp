<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserTable extends Migration
{
    public function up()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->unsignedBigInteger('user_groups_id')->nullable();
                $table->string('billy_account_id')->nullable();
                $table->timestamp('billy_created_at')->nullable();
                $table->timestamp('billy_updated_at')->nullable();
                $table->foreign('user_groups_id')
                    ->references('id')
                    ->on('user_groups')
                    ->onUpdate('cascade');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->dropForeign('user_groups_id');
                $table->dropColumn('billy_account_id');
                $table->dropColumn('billy_created_at');
                $table->dropColumn('billy_updated_at');
                $table->dropColumn('user_groups_id');
            }
        );
    }
}
