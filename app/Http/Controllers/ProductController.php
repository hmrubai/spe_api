<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CartItem;
use App\Models\Medicine;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\DamageItem;
use App\Models\ConsumerGood;
use App\Models\MedicineType;
use App\Models\MedicineCompany;
use App\Models\InventoryDetail;
use App\Models\Order;
use App\Models\OrderDue;
use App\Models\OrderItem;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use App\Exports\PurchaseExport;
use Illuminate\Support\Facades\App;
use Validator;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
  public function index(Request $request)
  {
      $data = $request->query();
      $pageNo = $request->query('page_no') ?? 1;
      $limit = $request->query('limit') ?? 500;
      $offset = (($pageNo - 1) * $limit);
      $where = array();
      $user = $request->auth;
      // $where = array_merge(array(['sales.pharmacy_branch_id', $user->pharmacy_branch_id]), $where);
      if (!empty($data['company_name'])) {
          $where = array_merge(array(['medicine_companies.company_name', 'LIKE', '%' . $data['company_name'] . '%']), $where);
      }
      if (!empty($data['customer_mobile'])) {
          $where = array_merge(array(['sales.customer_mobile', 'LIKE', '%' . $data['customer_mobile'] . '%']), $where);
      }
      if (!empty($data['sale_date'])) {
          $dateRange = explode(',',$data['sale_date']);
          // $query = Sale::where($where)->whereBetween('created_at', $dateRange);
          $where = array_merge(array([DB::raw('DATE(created_at)'), '>=', $dateRange[0]]), $where);
          $where = array_merge(array([DB::raw('DATE(created_at)'), '<=', $dateRange[1]]), $where);
      }
      $query = Medicine::where($where)
            ->join('medicine_companies', 'medicines.company_id', '=', 'medicine_companies.id')
            ->join('medicine_types', 'medicines.medicine_type_id', '=', 'medicine_types.id');
      $total = $query->count();
      $products = $query
          ->select('medicines.id as medicine_id','medicines.generic_name','medicines.company_id as company_id','medicines.medicine_type_id as medicine_type_id','medicines.brand_name','medicines.strength','medicine_types.name as medicine_type','medicine_companies.company_name as medicine_company')
          ->orderBy('medicines.brand_name', 'asc')
          ->offset($offset)
          ->limit($limit)
          ->get();
      $data = array(
          'total' => $total,
          'data' => $products,
          'page_no' => $pageNo,
          'limit' => $limit,
      );
      return response()->json($data);
  }

}
