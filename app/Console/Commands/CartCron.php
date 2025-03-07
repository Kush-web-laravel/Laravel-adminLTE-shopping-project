<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CartItem;
use Carbon\Carbon;

class CartCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:delete-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete items from the cart if that items has not been ordered since an hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("Cron Job running at " . now());
        $currentTime = Carbon::now();
        $allItems = CartItem::all();
        $deletedCount = 0;

        foreach ($allItems as $item) {
            $timeDifference = ($item->created_at)->diffInMinutes($currentTime);

            if ($timeDifference > 60) {
                info("Deleting item created at " . $item->created_at->toDateTimeString() . " with time difference: " . $timeDifference . " minutes");
                $item->delete();
                $deletedCount++;
            }
            else {
                info($item->created_at->toDateTimeString() . " with time difference: " . $timeDifference. " minutes");
            }
        }

        if ($deletedCount > 0) {
            info("Deleted $deletedCount old cart items.");
        } else {
            info("No items to delete.");
        }
    }
}
