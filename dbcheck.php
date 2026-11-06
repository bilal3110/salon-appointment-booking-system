<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "--- bookings sample --" . PHP_EOL;
foreach (DB::table('bookings')->limit(8)->get() as $b) {
    echo "date={$b->booking_date} time={$b->booking_time} status={$b->status}" . PHP_EOL;
}
echo "--- distinct times --" . PHP_EOL;
foreach (DB::table('bookings')->distinct()->orderBy('booking_time')->pluck('booking_time') as $t) { echo "[$t]" . PHP_EOL; }
echo "--- staff photos --" . PHP_EOL;
foreach (DB::table('staff')->get() as $s) { echo "{$s->id} user={$s->user_id} photo=" . var_export($s->photo, true) . PHP_EOL; }
echo "--- testimonials --" . PHP_EOL;
foreach (DB::table('testimonials')->get() as $t) { echo "{$t->id} {$t->name} | {$t->user_profession} | rating={$t->rating}" . PHP_EOL; }
