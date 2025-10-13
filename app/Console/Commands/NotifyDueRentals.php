<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DueDateReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NotifyDueRentals extends Command
{
    protected $signature = 'rentals:notify-due {--days=2 : Días antes del vencimiento para avisar}';
    protected $description = 'Envía correos por rentas próximas a vencer o vencidas';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $rows = DB::table('rental as r')
            ->join('inventory as i','i.inventory_id','=','r.inventory_id')
            ->join('film as f','f.film_id','=','i.film_id')
            ->join('customer as c','c.customer_id','=','r.customer_id')
            ->leftJoin('customer_user as cu','cu.customer_id','=','c.customer_id')
            ->leftJoin('users as u','u.id','=','cu.user_id')
            ->select('r.rental_id','r.rental_date','f.title as film_title','f.rental_duration','c.first_name','c.last_name','c.email','u.id as user_id')
            ->whereNull('r.return_date')
            ->get();

        $count = 0;
        foreach ($rows as $row) {
            $due = (new \DateTime($row->rental_date))->modify("+{$row->rental_duration} days");
            $now = new \DateTime('now');
            $diffDays = (int) floor(($due->getTimestamp() - $now->getTimestamp()) / 86400);
            $overdue = $diffDays < 0;
            $shouldNotify = ($diffDays === $days) || $overdue;
            if (!$shouldNotify) continue;

            $name = trim(($row->first_name ?? '').' '.($row->last_name ?? '')) ?: 'Cliente';

            if ($row->user_id) {
                $user = User::find($row->user_id);
                if ($user && $user->email) {
                    $user->notify(new DueDateReminder($name, $row->film_title, $due, $overdue));
                    $count++;
                    continue;
                }
            }

            if (!empty($row->email)) {
                \Illuminate\Support\Facades\Notification::route('mail', $row->email)
                    ->notify(new DueDateReminder($name, $row->film_title, $due, $overdue));
                $count++;
            }
        }

        $this->info("Correos enviados: {$count}");
        return self::SUCCESS;
    }
}
