<?php

namespace Database\Seeders\OrderSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Create five sample records for each order flow.
     *
     * In this application, private tour uses the private boat order flow.
     */
    public function run(): void
    {
        $user = DB::table('users')->orderBy('id')->first();

        if (!$user) {
            $this->command?->warn('OrderSeeder skipped: run UserSeeder first.');
            return;
        }

        DB::transaction(function () use ($user): void {
            $this->syncExistingSeedOrderEmails($user->email);
            $this->seedFastBoatOrders($user->id, $user->email);
            $this->seedPrivateTourOrders($user->id, $user->email);
            $this->seedActivityOrders($user->id, $user->email);
        });

        $this->command?->info('Sample order seeding completed: 5 fast boat, 5 private tour, 5 activity.');
    }

    private function syncExistingSeedOrderEmails(string $userEmail): void
    {
        foreach (['fast_boat_orders', 'private_boat_orders', 'activity_orders'] as $table) {
            DB::table($table)->where('uuid', 'like', 'seed-%')->update(['pic_email' => $userEmail]);
        }
    }

    private function seedFastBoatOrders(int $userId, string $userEmail): void
    {
        $schedules = DB::table('fast_boat_schedules')->where('is_active', 1)->orderBy('id')->get();

        for ($index = 0; $index < 5; $index++) {
            $number = $index + 1;
            $schedule = $schedules[$index % $schedules->count()] ?? null;
            $variant = $schedule
                ? DB::table('fast_boat_schedule_variants')->where('fast_boat_schedule_id', $schedule->id)->orderBy('id')->first()
                : null;
            $boat = $schedule ? DB::table('fast_boats')->where('id', $schedule->fast_boat_id)->first() : null;
            $departure = $schedule ? DB::table('harbors')->where('id', $schedule->departure_harbor_id)->first() : null;
            $arrival = $schedule ? DB::table('harbors')->where('id', $schedule->arrival_harbor_id)->first() : null;

            if (!$schedule || !$variant || !$boat || !$departure || !$arrival) continue;

            $uuid = "seed-fast-boat-{$number}";
            if (DB::table('fast_boat_orders')->where('uuid', $uuid)->exists()) continue;

            $quantity = 2;
            $total = $variant->price * $quantity;
            $status = $this->statuses()[$index];
            $orderId = DB::table('fast_boat_orders')->insertGetId($this->orderValues(
                $uuid, $userId, $userEmail, $total, $status, $number, $number + 1, true
            ));

            DB::table('fast_boat_order_details')->insert([
                'order_id' => $orderId,
                'fast_boat_id' => $boat->id,
                'schedule_id' => $schedule->id,
                'schedule_variant_id' => $variant->id,
                'departure_harbor_id' => $departure->id,
                'arrival_harbor_id' => $arrival->id,
                'departure_harbor_name' => $departure->name,
                'arrival_harbor_name' => $arrival->name,
                'departure_time' => $schedule->departure_time,
                'arrival_time' => $schedule->arrival_time,
                'passenger_type' => $variant->passenger_type,
                'age_type' => $variant->age_type,
                'quantity' => $quantity,
                'name' => $boat->name,
                'price' => $variant->price,
                'total_price' => $total,
                'image' => $boat->image,
                'created_at' => now()->subDays($number),
            ]);

            for ($passenger = 1; $passenger <= $quantity; $passenger++) {
                DB::table('fast_boat_order_forms')->insert([
                    'order_id' => $orderId,
                    'schedule_id' => $schedule->id,
                    'variant_id' => $variant->id,
                    'name' => "Seed Passenger {$number}-{$passenger}",
                    'nationality' => 'Indonesia',
                    'passenger_type' => $variant->passenger_type,
                    'age_type' => $variant->age_type,
                    'is_local' => 1,
                    'is_itas_holder' => 0,
                    'created_at' => now()->subDays($number),
                    'updated_at' => now()->subDays($number),
                ]);
            }

            $this->seedPayment($orderId, null, null, $total, 'fast_boat', $status, $number);
        }
    }

    private function seedPrivateTourOrders(int $userId, string $userEmail): void
    {
        $boats = DB::table('private_boats')->where('is_active', 1)->orderBy('id')->get();
        $harbors = DB::table('harbors')->orderBy('id')->get();

        for ($index = 0; $index < 5; $index++) {
            $number = $index + 1;
            $boat = $boats[$index % max(1, $boats->count())] ?? null;
            $harbor = $boat ? DB::table('harbors')->where('id', $boat->pickup_harbor_id)->first() : null;
            $harbor ??= $harbors->first();
            $variant = $boat
                ? DB::table('private_boat_variants')->where('private_boat_id', $boat->id)->orderBy('id')->first()
                : null;

            if (!$boat || !$variant || !$harbor) continue;

            $uuid = "seed-private-tour-{$number}";
            if (DB::table('private_boat_orders')->where('uuid', $uuid)->exists()) continue;

            $total = $variant->price;
            $status = $this->statuses()[$index];
            $orderId = DB::table('private_boat_orders')->insertGetId($this->orderValues(
                $uuid, $userId, $userEmail, $total, $status, $number + 10, $number + 2
            ));

            DB::table('private_boat_order_details')->insert([
                'order_id' => $orderId,
                'product_id' => $boat->id,
                'variant_id' => $variant->id,
                'pickup_harbor_id' => $harbor->id,
                'pickup_harbor_name' => $harbor->name,
                'quantity' => 1,
                'name' => $boat->name,
                'price' => $variant->price,
                'total_price' => $total,
                'image' => null,
                'message' => 'Seed order for local development.',
                'created_at' => now()->subDays($number),
            ]);

            DB::table('private_boat_order_forms')->insert([
                'order_id' => $orderId,
                'product_id' => $boat->id,
                'variant_id' => $variant->id,
                'name' => "Seed Passenger {$number}",
                'nationality' => 'Indonesia',
                'created_at' => now()->subDays($number),
                'updated_at' => now()->subDays($number),
            ]);

            $this->seedPayment(null, $orderId, null, $total, 'private_boat', $status, $number + 10);
        }
    }

    private function seedActivityOrders(int $userId, string $userEmail): void
    {
        $activities = DB::table('activities')->where('is_active', 1)->orderBy('id')->get();

        for ($index = 0; $index < 5; $index++) {
            $number = $index + 1;
            $activity = $activities[$index % max(1, $activities->count())] ?? null;
            $variant = $activity
                ? DB::table('activity_variants')->where('activity_id', $activity->id)->orderBy('id')->first()
                : null;
            $location = $activity ? DB::table('locations')->where('id', $activity->location_id)->first() : null;

            if (!$activity || !$variant || !$location) continue;

            $uuid = "seed-activity-{$number}";
            if (DB::table('activity_orders')->where('uuid', $uuid)->exists()) continue;

            $total = $variant->price;
            $status = $this->statuses()[$index];
            $orderId = DB::table('activity_orders')->insertGetId($this->orderValues(
                $uuid, $userId, $userEmail, $total, $status, $number + 20, $number + 4
            ));

            DB::table('activity_order_details')->insert([
                'order_id' => $orderId,
                'product_id' => $activity->id,
                'variant_id' => $variant->id,
                'location_id' => $location->id,
                'location_name' => $location->name,
                'passenger_type' => 'adult',
                'quantity' => 1,
                'product_name' => $activity->name,
                'variant_name' => $variant->name,
                'price' => $variant->price,
                'total_price' => $total,
                'image' => null,
                'created_at' => now()->subDays($number),
                'updated_at' => now()->subDays($number),
            ]);

            DB::table('activity_order_forms')->insert([
                'order_id' => $orderId,
                'product_id' => $activity->id,
                'variant_id' => $variant->id,
                'name' => "Seed Passenger {$number}",
                'nationality' => 'Indonesia',
                'created_at' => now()->subDays($number),
                'updated_at' => now()->subDays($number),
            ]);

            $this->seedPayment(null, null, $orderId, $total, 'activity', $status, $number + 20);
        }
    }

    private function orderValues(string $uuid, int $userId, string $userEmail, $total, string $status, int $number, int $days, bool $withReturnDate = false): array
    {
        $values = [
            'uuid' => $uuid,
            'user_id' => $userId,
            'promo_amount' => 0,
            'total_purchased' => $total,
            'grand_total_purchased' => $total,
            'pic_name' => "Seed Customer {$number}",
            'pic_email' => $userEmail,
            'pic_phone_number' => '+628123456789',
            'status' => $status,
            'activity_date' => now()->addDays($days)->toDateString(),
            'created_at' => now()->subDays($number),
            'updated_at' => now()->subDays($number),
        ];

        if ($withReturnDate) $values['return_date'] = now()->addDays($days + 1)->toDateString();

        return $values;
    }

    private function seedPayment(?int $fastBoatOrderId, ?int $privateBoatOrderId, ?int $activityOrderId, $total, string $type, string $orderStatus, int $number): void
    {
        if (!in_array($orderStatus, ['paid', 'failed'], true)) return;

        DB::table('order_payments')->insert([
            'fast_boat_order_id' => $fastBoatOrderId,
            'private_boat_order_id' => $privateBoatOrderId,
            'activity_order_id' => $activityOrderId,
            'invoice_id' => "seed-invoice-{$type}-{$number}",
            'payment_method' => 'credit_card',
            'payment_channel' => 'seed',
            'paid_at' => $orderStatus === 'paid' ? now()->subDays($number) : null,
            'expired_at' => $orderStatus === 'failed' ? now()->subDays($number) : now()->addDay(),
            'status' => $orderStatus === 'paid' ? 'paid' : 'failed',
            'type' => $type,
            'is_refunded' => 0,
            'total_purchased' => $total,
            'url' => 'http://localhost/seed-payment',
            'created_at' => now()->subDays($number),
            'updated_at' => now()->subDays($number),
        ]);
    }

    private function statuses(): array
    {
        return ['waiting_for_payment', 'paid', 'paid', 'canceled', 'failed'];
    }
}
