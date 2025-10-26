<?php

use app\core\Database\Migration;
use app\core\Database\Schema;
use app\core\Database\Blueprint;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
