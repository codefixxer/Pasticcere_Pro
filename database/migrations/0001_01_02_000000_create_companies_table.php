<?php

// database/migrations/xxxx_xx_xx_create_companies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    public function up()
    {
        Schema::create('companies', function(Blueprint $table){
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable();
          
            $table->string('name')->unique();
            $table->timestamps();

            
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
