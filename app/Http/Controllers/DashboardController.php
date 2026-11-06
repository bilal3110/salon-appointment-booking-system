<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Testimonials;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::guard()->user();

        $servicesCount = Service::count();
        $staffCount = Staff::count();

        $bookings = $this->scopedBookings($currentUser);

        $totalAppointments = (clone $bookings)->count();
        $pendingBookingsCount = (clone $bookings)->where('status', 'pending')->count();
        $confirmedCount = (clone $bookings)->where('status', 'confirmed')->count();
        $completedCount = (clone $bookings)->where('status', 'completed')->count();
        $canceledCount = (clone $bookings)->where('status', 'canceled')->count();
        $todayCount = (clone $bookings)->whereDate('booking_date', today())->count();
        $clientsCount = (clone $bookings)->distinct()->count('customer_email');

        $revenue = (float) (clone $bookings)
            ->where('status', 'completed')
            ->join('services', 'services.id', '=', 'bookings.service_id')
            ->sum('services.price');

        $todayBookings = (clone $bookings)
            ->with(['staff.user', 'service'])
            ->whereDate('booking_date', today())
            ->orderBy('booking_time')
            ->get();

        $pendingQueue = (clone $bookings)
            ->with(['staff.user', 'service'])
            ->where('status', 'pending')
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->limit(6)
            ->get();

        $statusBreakdown = [
            ['key' => 'pending', 'label' => 'Pending', 'count' => $pendingBookingsCount, 'color' => 'var(--warn)'],
            ['key' => 'confirmed', 'label' => 'Confirmed', 'count' => $confirmedCount, 'color' => 'var(--ok)'],
            ['key' => 'completed', 'label' => 'Completed', 'count' => $completedCount, 'color' => 'var(--ivory-dim)'],
            ['key' => 'canceled', 'label' => 'Cancelled', 'count' => $canceledCount, 'color' => 'var(--bad)'],
        ];

        $dailySeries = $this->dailySeries($bookings, 14);

        $topServices = (clone $bookings)
            ->join('services', 'services.id', '=', 'bookings.service_id')
            ->select('services.service_name', 'services.service_type', 'services.price')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('SUM(CASE WHEN bookings.status = "completed" THEN services.price ELSE 0 END) as revenue')
            ->groupBy('services.service_name', 'services.service_type', 'services.price')
            ->orderByDesc('bookings_count')
            ->limit(6)
            ->get();

        return view('dashboard.index', compact(
            'servicesCount',
            'staffCount',
            'pendingBookingsCount',
            'totalAppointments',
            'confirmedCount',
            'completedCount',
            'canceledCount',
            'todayCount',
            'clientsCount',
            'revenue',
            'todayBookings',
            'pendingQueue',
            'statusBreakdown',
            'dailySeries',
            'topServices'
        ));
    }

    public function servicePage()
    {
        return view('dashboard.service.index');
    }

    public function createServicePage()
    {
        return view('dashboard.service.create');
    }

    public function editServicePage($id)
    {
        return view('dashboard.service.edit', ['id' => $id]);
    }

    public function addUserPage()
    {
        return view('dashboard.user.index');
    }

    public function createUserPage()
    {
        return view('dashboard.user.create');
    }

    public function editUserPage($id)
    {
        return view('dashboard.user.edit', ['id' => $id]);
    }

    public function addStaffPage()
    {
        return view('dashboard.staff.index');
    }

    public function viewStaffPage($id)
    {
        return view('dashboard.staff.view', ['id' => $id]);
    }

    public function createStaffPage()
    {
        $users = User::where('type', 'staff')->get();
        $services = Service::all();
        return view('dashboard.staff.create', compact('users', 'services'));
    }

    public function editStaffPage($id)
    {
        $staff = Staff::with(['user', 'services'])->findOrFail($id);
        $users = User::where('type', 'staff')->get();
        $services = Service::all();

        return view('dashboard.staff.edit', compact('staff', 'users', 'services'));
    }

    public function getBookingsPage()
    {
        return view('dashboard.bookings.index');
    }

    public function clientsPage()
    {
        $this->requireAdmin();

        $clients = DB::table('bookings')
            ->join('services', 'services.id', '=', 'bookings.service_id')
            ->select('bookings.customer_name', 'bookings.customer_email', 'bookings.customer_contact')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('SUM(CASE WHEN bookings.status = "completed" THEN services.price ELSE 0 END) as spend')
            ->selectRaw('MAX(bookings.booking_date) as last_visit')
            ->groupBy('bookings.customer_name', 'bookings.customer_email', 'bookings.customer_contact')
            ->orderByDesc('bookings_count')
            ->orderBy('bookings.customer_name')
            ->get();

        return view('dashboard.clients.index', compact('clients'));
    }

    public function testimonialsPage()
    {
        $this->requireAdmin();

        $testimonials = Testimonials::latest()->get();
        $averageRating = $testimonials->avg('rating');

        return view('dashboard.testimonials.index', compact('testimonials', 'averageRating'));
    }

    public function reportsPage()
    {
        $this->requireAdmin();

        $bookings = $this->scopedBookings(Auth::guard()->user());

        $dailySeries = $this->dailySeries($bookings, 30);

        $statusBreakdown = [
            ['key' => 'pending', 'label' => 'Pending', 'count' => (clone $bookings)->where('status', 'pending')->count(), 'color' => 'var(--warn)'],
            ['key' => 'confirmed', 'label' => 'Confirmed', 'count' => (clone $bookings)->where('status', 'confirmed')->count(), 'color' => 'var(--ok)'],
            ['key' => 'completed', 'label' => 'Completed', 'count' => (clone $bookings)->where('status', 'completed')->count(), 'color' => 'var(--ivory-dim)'],
            ['key' => 'canceled', 'label' => 'Cancelled', 'count' => (clone $bookings)->where('status', 'canceled')->count(), 'color' => 'var(--bad)'],
        ];

        $servicePerformance = (clone $bookings)
            ->join('services', 'services.id', '=', 'bookings.service_id')
            ->select('services.service_name', 'services.service_type', 'services.price')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('SUM(CASE WHEN bookings.status = "completed" THEN services.price ELSE 0 END) as revenue')
            ->groupBy('services.service_name', 'services.service_type', 'services.price')
            ->orderByDesc('revenue')
            ->orderByDesc('bookings_count')
            ->get();

        $staffPerformance = DB::table('bookings')
            ->join('staff', 'staff.id', '=', 'bookings.staff_id')
            ->join('users', 'users.id', '=', 'staff.user_id')
            ->join('services', 'services.id', '=', 'bookings.service_id')
            ->select('users.name')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('SUM(CASE WHEN bookings.status = "completed" THEN 1 ELSE 0 END) as completed_count')
            ->selectRaw('SUM(CASE WHEN bookings.status = "completed" THEN services.price ELSE 0 END) as revenue')
            ->groupBy('users.name')
            ->orderByDesc('revenue')
            ->get();

        $completedCount = (clone $bookings)->where('status', 'completed')->count();
        $revenue = (float) (clone $bookings)
            ->where('status', 'completed')
            ->join('services', 'services.id', '=', 'bookings.service_id')
            ->sum('services.price');

        $totals = [
            'appointments' => (clone $bookings)->count(),
            'completed' => $completedCount,
            'revenue' => $revenue,
            'average_ticket' => $completedCount > 0 ? $revenue / $completedCount : 0.0,
            'clients' => (clone $bookings)->distinct()->count('customer_email'),
        ];

        return view('dashboard.reports.index', compact(
            'dailySeries',
            'statusBreakdown',
            'servicePerformance',
            'staffPerformance',
            'totals'
        ));
    }

    public function settingsPage()
    {
        return view('dashboard.setting.setting', [
            'currentUser' => Auth::guard()->user(),
        ]);
    }

    public function loginPage()
    {
        return view('dashboard.login');
    }
    private function scopedBookings($user)
    {
        $query = Booking::query();

        if ($user->type !== 'admin') {
            $staff = Staff::where('user_id', $user->id)->first();
            $query->where('staff_id', $staff ? $staff->id : -1);
        }

        return $query;
    }
    private function dailySeries($bookings, $days)
    {
        $from = today()->subDays($days - 1);

        $rows = (clone $bookings)
            ->whereBetween('booking_date', [$from->toDateString(), today()->toDateString()])
            ->selectRaw('booking_date, COUNT(*) as total')
            ->groupBy('booking_date')
            ->pluck('total', 'booking_date');

        $series = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $series[] = [
                'date' => $date->toDateString(),
                'label' => $date->format('j M'),
                'total' => (int) ($rows[$date->toDateString()] ?? 0),
            ];
        }

        return $series;
    }

    private function requireAdmin()
    {
        if (Auth::guard()->user()->type !== 'admin') {
            abort(403, 'This section is reserved for the proprietor.');
        }
    }
}
