<?php
// database/migrations/YYYY_MM_DD_add_profile_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('vat')->nullable()->after('email'); // Vat field
            $table->text('address')->nullable()->after('vat'); // Address field
            $table->string('photo')->nullable()->after('address'); // Photo field
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['vat', 'address', 'photo']);
        });
    }
}
