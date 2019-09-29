<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function search(Request $request)
    {
        $str = $request->input('search');

        $companyData = $request->input('company') ? MedicineCompany::where('company_name', 'like', $request->input('company'))->first() : 0;

        $company_id =  $companyData ? $companyData->id : 0;

        $medicines = Medicine::where('brand_name', 'like', $str . '%')
            ->when($company_id, function ($query, $company_id) {
                return $query->where('company_id', $company_id);
            })
            ->orWhere('id', $str)
            ->inRandomOrder()
            ->limit(10)
            ->get();
        $data = array();
        foreach ($medicines as $medicine) {
            $medicineStr = $medicine->brand_name . ' (' . $medicine->strength . ',' . $medicine->medicineType->name . ')';
            $data[] = ['id' => $medicine->id, 'name' => $medicineStr];
        }
        return response()->json($data);
    }

    public function searchByPharmacy(Request $request)
    {
        $str = $request->input('search');
        $openSale = true;
        $pharmacyMedicineIds = $openSale ? false : DB::table('inventories')->select('medicine_id')->distinct()->pluck('medicine_id');

        $medicines = Medicine::where('brand_name', 'like', $str . '%')
            ->when($pharmacyMedicineIds, function ($query, $pharmacyMedicineIds) {
                   return $query->whereIn('id', $pharmacyMedicineIds);
               })
            ->inRandomOrder()
            ->limit(10)
            ->get();
        $data = array();
        foreach ($medicines as $medicine) {
            $company = DB::table('medicine_companies')->where('id', $medicine->company_id)->first();
            $medicineType = $medicine->product_type == 1 ? $medicine->medicineType->name : ' CP';
            $medicineStr = $medicine->brand_name . ' (' . $medicine->strength . ',' . $medicineType . ')';
            $data[] = ['id'=>$medicine->id, 'name' => $medicineStr, 'company' => $company->company_name];
        }
        return response()->json($data);
    }

    public function batchList(Request $request)
    {
        $batches = DB::table('products')->where('medicine_id', $request->input('medicine_id'))->pluck('batch_no');

        return response()->json($batches);
    }

    public function getAvailableQuantity(Request $request)
    {
        $product = DB::table('products')
        ->select(DB::raw('SUM(quantity) as available_quantity'))
        ->where('medicine_id', $request->input('medicine_id'))
        ->first();

        return response()->json($product);
    }

    public function searchByCompany(Request $request)
    {
        $companyId = $request->input('company');
        $medicines = Medicine::where('company_id', $companyId)
            ->limit(100)
            ->get();
        $data = array();
        foreach ($medicines as $medicine) {
            $aData = array();
            $aData['id'] = $medicine->id;
            $aData['brand_name'] = $medicine->brand_name;
            $aData['generic_name'] = $medicine->generic_name;
            $aData['strength'] = $medicine->strength;
            $aData['dar_no'] = $medicine->dar_no;
            $aData['price_per_pcs'] = $medicine->price_per_pcs;
            $aData['price_per_box'] = $medicine->price_per_box;
            $aData['price_per_strip'] = $medicine->price_per_strip;
            $aData['pcs_per_box'] = $medicine->pcs_per_box;
            $aData['pcs_per_strip'] = $medicine->pcs_per_strip;

            $aData['medicine_type'] = $medicine->medicineType->name;

            $data[] = $aData;
        }

        return response()->json($data);
    }
}
