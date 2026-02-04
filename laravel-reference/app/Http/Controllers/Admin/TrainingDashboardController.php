<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrainingDashboardController extends Controller
{
    public function index()
    {
        return view('admin.training-dashboard.index');
    }

    public function generate_new_password()
    {
        $new_password = Str::random(10);

        DB::connection('mysql_training_dashboard')
            ->table('users')
            ->where('name', '=', 'training')
            ->update([
                'password' => bcrypt($new_password),
                'password_changed_at' => now(),
            ]);

        session()->flash('training_dashboard_password_generated', [
            'new_password' => $new_password,
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('admin.training-dashboard.index');
    }
}
