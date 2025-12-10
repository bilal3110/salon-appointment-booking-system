<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
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
        return view('dashboard.staff.create',compact('users','services'));
    }
}
