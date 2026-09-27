<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SystemSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        SystemSetting::updateOrCreate(
            ['key' => 'country_filter_token_fee'],
            [
                'value' => '5',
                'category' => 'app_rules',
                'description' => 'Token Fee to Filter Users by Country'
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        SystemSetting::where('key', 'country_filter_token_fee')->delete();
    }
};
