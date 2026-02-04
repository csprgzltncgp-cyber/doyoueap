<?php

namespace Database\Seeders;

use App\Models\AffiliateSearch;
use App\Models\AffiliateSearchComment;
use App\Models\AffiliateSearchCompletionPoint;
use App\Models\ExpertData;
use App\Models\Invoice;
use App\Models\InvoiceData;
use App\Models\OperatorData;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskCompletionPoint;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemoveUsersTasksAffiliateSearchesComments extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users_to_remove = User::withTrashed()->whereIn('id', [801, 167])->get();

        foreach ($users_to_remove as $user) {
            $task_ids = Task::query()->where('to_id', $user->id)->pluck('id');
            $search_ids = AffiliateSearch::query()->where('to_id', $user->id)->pluck('id');

            // 1. Delete every task comments
            TaskComment::query()->whereIn('id', $task_ids)->forceDelete();

            // 2. Delete every task completion points
            TaskCompletionPoint::query()->whereIn('task_id', $task_ids)->forceDelete();

            // 3. Delete tasks
            Task::query()->where('to_id', $user->id)->forceDelete();

            // 4. Delete affiliate search comments
            AffiliateSearchComment::query()->whereIn('id', $search_ids)->forceDelete();

            // 5. Delete affiliate search completion points
            AffiliateSearchCompletionPoint::query()->where('user_id', $user->id)->forceDelete();

            // 6. Delete affiliate searches
            AffiliateSearch::query()->where('to_id', $user->id)->forceDelete();

            // 7. Delete operator datas
            OperatorData::query()->where('user_id', $user->id)->forceDelete();

            // 8. Remove connected account
            $connectingUsers = User::query()->withTrashed()->where('connected_account', $user->id)->get();
            foreach ($connectingUsers as $user) {
                $user->connected_account = null;
                $user->save();
            }

            // 9. Remove invoice downloaded by
            $invoices = Invoice::query()->withTrashed()->where('downloaded_by', $user->id)->get();
            foreach ($invoices as $invoice) {
                $invoice->downloaded_by = null;
                $invoice->save();
            }

            // 10. Delete expert_X_country
            DB::table('expert_x_country')->where('expert_id', $user->id)->delete();

            // 11. Delete expert data
            ExpertData::query()->where('user_id', $user->id)->forceDelete();

            // 12. Delete invoice data
            InvoiceData::query()->where('user_id', $user->id)->forceDelete();

            // 13. Delete user_x_permission
            DB::table('user_x_permission')->where('user_id', $user->id)->delete();

            // 14. Delete users_to_all_city
            DB::table('users_to_all_city')->where('user_id', $user->id)->delete();

            // 15. Delete expert slaries
            DB::table('expert_salaries')->where('user_id', $user->id)->delete();

            // 16. Delete expert cases
            DB::table('expert_x_case')->where('user_id', $user->id)->delete();

            // 17. Delete user city
            DB::table('user_x_city')->where('user_id', $user->id)->delete();

            // 18. Delete user
            User::query()->where('id', $user->id)->forceDelete();
        }
    }
}
