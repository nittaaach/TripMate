<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionalAddressController extends Controller
{
    /**
     * Get all provinces.
     */
    public function provinces(): JsonResponse
    {
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        return response()->json($provinces);
    }

    /**
     * Get regencies by province_id.
     */
    public function regencies(Request $request): JsonResponse
    {
        $provinceId = $request->query('province_id');

        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'province_id', 'name', 'type']);

        return response()->json($regencies);
    }

    /**
     * Get districts by regency_id.
     */
    public function districts(Request $request): JsonResponse
    {
        $regencyId = $request->query('regency_id');

        $districts = District::where('regency_id', $regencyId)
            ->orderBy('name')
            ->get(['id', 'regency_id', 'name']);

        return response()->json($districts);
    }

    /**
     * Get villages by district_id.
     */
    public function villages(Request $request): JsonResponse
    {
        $districtId = $request->query('district_id');

        $villages = Village::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'district_id', 'name', 'postal_code']);

        return response()->json($villages);
    }
}
