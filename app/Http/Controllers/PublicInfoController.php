<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Polyclinic;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\View\View;

class PublicInfoController extends Controller
{
    public function about(): View
    {
        $hospitalInfo = Setting::get('hospital_info', []);
        $totalDoctors = Doctor::where('is_active', true)->count();
        $totalPolyclinics = Polyclinic::where('is_active', true)->count();
        $totalRooms = Room::where('is_active', true)->count();

        return view('public.about', compact('hospitalInfo', 'totalDoctors', 'totalPolyclinics', 'totalRooms'));
    }

    public function patientFlow(): View
    {
        return view('public.patient-flow');
    }

    public function patientRights(): View
    {
        return view('public.patient-rights');
    }

    public function privacyPolicy(): View
    {
        return view('public.privacy-policy');
    }

    public function faq(): View
    {
        return view('public.faq');
    }

    public function contact(): View
    {
        $hospitalInfo = Setting::get('hospital_info', []);

        return view('public.contact', compact('hospitalInfo'));
    }
}
