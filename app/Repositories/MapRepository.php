<?php

namespace App\Repositories;

use DB;
use Carbon\Carbon;
use Exception;
use App\Models\PO;
use App\Models\M_SETTING;

use App\Interfaces\POInterface;
use App\Enums\ESettingType;
use App\Enums\EStatusType;
use App\Models\contact_histories;
use App\Models\contact_files;
use App\Models\contacts;
use App\Models\facilities;
use App\Models\facility_files;
use App\Models\facility_urls;
use App\Repositories\Interfaces\MapRepositoryInterface;
use App\Models\kiroposts;
use App\Models\landmarks;
use App\Models\Masters\master_contact_categories;
use App\Models\Masters\master_contact_category_details;
use App\Models\Masters\master_contact_types;
use App\Models\Masters\master_contact_ways;
use App\Models\Masters\master_facility_types;
use App\Models\Masters\master_landmark_types;
use App\Models\Masters\master_offices;
use App\Models\Masters\master_road_types;
use App\Models\Masters\master_up_and_downs;
use App\Models\movies;
use App\Models\menu_larges;
use App\Models\menu_media;
use App\Models\menu_smalls;

use function Psy\debug;

class MapRepository implements MapRepositoryInterface
{

   // Start get master
   function getListMasterFacilityTypes()
   {
      $items = master_facility_types::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }
   function getListMasterOffice()
   {
      $items = master_offices::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }
   function getListMasterRoadTypes()
   {
      $items = master_road_types::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }
   function getListMasterUpAndDowns()
   {
      $items = master_up_and_downs::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }
   function getListMasterLandmarkTypes()
   {
      $items = master_landmark_types::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }

   function getListMasterContactTypes()
   {
      $items = master_contact_types::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }

   function getListMasterContactCategories()
   {
      $items = master_contact_categories::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }

   function getListMasterContactWay(){
      $items = master_contact_ways::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }

   // End get master


   public function getListKiroposts()
   {
      $items = kiroposts::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }
   public function getTableLabels($officeId = '', $roadTypeId = '', $roadNo = '', $upAndDownId = '', $columnSort = '', $optionSort = '', $pagination = 20)
   {
      $items = kiroposts::select(
         'kiroposts.*',
         'master_offices.name as office_name',
         'master_road_types.name as road_type_name',
         'master_up_and_downs.name as up_and_down_name'
      );
      $items = $items->leftjoin('master_offices', 'master_offices.id', 'kiroposts.office_id')
         ->leftjoin('master_road_types', 'master_road_types.id', 'kiroposts.road_type_id')
         ->leftjoin('master_up_and_downs', 'master_up_and_downs.id', 'kiroposts.up_and_down_id');
      if ($officeId) {
         $items = $items->where("office_id", $officeId);
      }
      if ($roadTypeId) {
         $items = $items->where("road_type_id", $roadTypeId);
      }
      if ($roadNo !== '') {
         $items = $items->where('road_no', 'like', '%' . $roadNo . '%');
      }
      if ($upAndDownId) {
         $items = $items->where("up_and_down_id", $upAndDownId);
      }
      if ($columnSort && $optionSort) {
         $items = $items->orderby($columnSort, $optionSort);
      }
      $items = $items->paginate($pagination);
      return $items;
   }

   public function getTableFacility($facilityTypeId = '', $officeId = '', $roadTypeId = '', $roadNo = '', $facilityName = '', $columnSort = '', $optionSort = '', $pagination = 20)
   {
      $items = facilities::select(
         'facilities.*',
         'master_offices.name as office_name',
         'master_road_types.name as road_type_name',
         'master_facility_types.name as facility_types_name',
         'master_facility_types.color as facility_types_color',
      );
      $items = $items->leftjoin('master_offices', 'master_offices.id', 'facilities.office_id')
         ->leftjoin('master_road_types', 'master_road_types.id', 'facilities.road_type_id')
         ->leftjoin('master_facility_types', 'master_facility_types.id', 'facilities.facility_type_id');
      if ($facilityTypeId) {
         $items = $items->where("facility_type_id", $facilityTypeId);
      }
      if ($officeId) {
         $items = $items->where("office_id", $officeId);
      }
      if ($roadTypeId) {
         $items = $items->where("road_type_id", $roadTypeId);
      }
      if ($roadNo !== '') {
         $items = $items->where('road_no', 'like', '%' . $roadNo . '%');
      }
      if ($facilityName !== '') {
         $items = $items->where('facilities.name', 'like', '%' . $facilityName . '%');
      }
      if ($columnSort && $optionSort) {
         $items = $items->orderby($columnSort, $optionSort);
      }
      $items = $items->paginate($pagination);
      return $items;
   }
   public function getTableContact($contactTypeId='',$contactCategoryId='',$report='',$createdAt='',$columnSort='',$optionSort='',$pagination=20)
   {
      $items = contacts::select(
         'contacts.*',
         'master_departments.name as contact_department_name',
         'master_contact_ways.name as contact_ways_name',
         'master_contact_types.name as contact_types_name',
         'master_contact_categories.name as contact_categories_name',
         'master_contact_category_details.name as contact_category_details_name',
      );
      $items = $items->leftjoin('master_departments', 'master_departments.id', 'contacts.reception_department_id')
         ->leftjoin('master_contact_ways', 'master_contact_ways.id', 'contacts.contact_way_id')
         ->leftjoin('master_contact_types', 'master_contact_types.id', 'contacts.contact_type_id')
         ->leftjoin('master_contact_categories', 'master_contact_categories.id', 'contacts.contact_category_id')
         ->leftjoin('master_contact_category_details', 'master_contact_category_details.id', 'contacts.contact_category_detail_id');
      if ($contactTypeId) {
         $items = $items->where("contact_type_id", $contactTypeId);
      }
      if ($contactCategoryId) {
         $items = $items->where("contacts.contact_category_id", $contactCategoryId);
      }
      if ($report) {
         $items = $items->where(function ($query) use ($report) {
             $query->where('reporter_address', 'like', '%' . $report . '%')
                   ->orWhere('reporter_name', 'like', '%' . $report . '%')
                   ->orWhere('reporter_tel', 'like', '%' . $report . '%');
         });
     }
      if ($createdAt) {
         $createdAtFormatted = date('Y-m-d', strtotime($createdAt));
         $items = $items->whereRaw("DATE(contacts.created_at) = ?", [$createdAtFormatted]);
      }
      if ($columnSort && $optionSort) {
         $items = $items->orderby($columnSort, $optionSort);
      }
      if($pagination){
         $items = $items->paginate($pagination);
      }else{
         $items = $items->get();
      }
      return $items;
   }
   public function getContactMaxId(){
      $item = contacts::where('id', contacts::max('id'))->first();
      return $item;
   }

   public function getListFacility($listId)
   {
      $items = facilities::select(
         'facilities.*',
         'master_offices.name as office_name',
         'master_road_types.name as road_type_name',
         'master_facility_types.name as facility_types_name',
         'master_facility_types.color as facility_types_color',
      );
      $items = $items->leftjoin('master_offices', 'master_offices.id', 'facilities.office_id')
         ->leftjoin('master_road_types', 'master_road_types.id', 'facilities.road_type_id')
         ->leftjoin('master_facility_types', 'master_facility_types.id', 'facilities.facility_type_id');
      if ($listId) {
         $items = $items->whereIn('facilities.facility_type_id', $listId);
      }
      $items = $items->get();
      return $items;
   }

   public function getListFacilityByMenu($menu_medium_id)
   {
      $items = facilities::select(
         'facilities.*',
         'master_offices.name as office_name',
         'master_road_types.name as road_type_name',
         'master_facility_types.name as facility_types_name',
         'master_facility_types.color as facility_types_color',
      );
      $items = $items->leftjoin('master_offices', 'master_offices.id', 'facilities.office_id')
         ->leftjoin('master_road_types', 'master_road_types.id', 'facilities.road_type_id')
         ->leftjoin('master_facility_types', 'master_facility_types.id', 'facilities.facility_type_id');
      if ($menu_medium_id) {
         $items = $items->whereIn('master_facility_types.menu_medium_id', $menu_medium_id);
      }
      $items = $items->get();
      return $items;
   }

   public function getFacilityAttribute($id)
   {
      $items = facilities::select('id','attribute')->where('id', $id)->first();
      return $items;
   }
   public function getFacilityFilesById($id)
   {
      $items = facility_files::where('facility_id', $id)->get();
      return $items;
   }
   public function getFacilityUrlsById($id)
   {
      $items = facility_urls::where('facility_id', $id)->get();
      return $items;
   }
   public function getListLandmarks()
   {
      $items = landmarks::query();
      $items = $items->leftjoin('master_landmark_types', 'landmarks.landmark_type_id', 'master_landmark_types.id');
      $items = $items->orderBy("landmarks.id", "asc")->get();
      return $items;
   }

   public function getTableLandmark($landmarkTypeId = '', $label = '', $columnSort = '', $optionSort = '', $pagination = 20)
   {
      $items = landmarks::select(
         'landmarks.*',
         'master_landmark_types.name as landmark_types_name',
         'master_landmark_types.color as landmark_types_color',
      );
      $items = $items->leftjoin('master_landmark_types', 'master_landmark_types.id', 'landmarks.landmark_type_id');
      if ($landmarkTypeId) {
         $items = $items->where("landmark_type_id", $landmarkTypeId);
      }
      if ($label !== '') {
         $items = $items->where('label', 'like', '%' . $label . '%');
      }
      if ($columnSort && $optionSort) {
         $items = $items->orderby($columnSort, $optionSort);
      }
      $items = $items->paginate($pagination);
      return $items;
   }

   public function getListMoviesDate()
   {
      $items = movies::selectRaw('EXTRACT(YEAR FROM created_at) AS year')
         ->selectRaw('EXTRACT(MONTH FROM created_at) AS month')
         ->selectRaw('EXTRACT(DAY FROM created_at) AS day')
         ->groupBy('year', 'month', 'day')
         ->orderBy('year', 'DESC')
         ->orderBy('month')
         ->orderBy('day')
         ->get();

      return $items;
   }

   public function getListMovie($listDate)
   {
      $query = movies::query();
      if ($listDate) {
          $items = $query->where(function ($query) use ($listDate) {
              foreach ($listDate as $date) {
                  $query->orWhereDate('created_at', $date);
              }
          });
      } else {
          $items = $query;
      }
      $items = $items->orderBy('frame')->get();
      return $items;
   }
   public function getMovieById($id){
      $items = movies::where('id',$id)->first();
      return $items;
   }
   public function getMenuLarges(){
      $items = menu_larges::query();
      $items = $items->OrderBy("sort", "asc")->get();
      return $items;
   }

   public function getMenuMedia($menu_large_id){
      $items = menu_media::select('id','menu_large_id','name','sort');
      $items = $items->where('menu_large_id',$menu_large_id);
      $items = $items->OrderBy("sort", "asc")->get();
      return $items;
   }
   public function getMenuSmalls($menu_medium_id){
      $items = menu_smalls::select('id','menu_medium_id','name','sort','type','url','heightoffset');
      $items = $items->where('menu_medium_id',$menu_medium_id);
      $items = $items->OrderBy("sort", "asc")->get();
      return $items;
   }

   public function getMenuSmallsFacility($menu_medium_id){
      $items = master_facility_types::select('id','name','color');
      $items = $items->where('menu_medium_id',$menu_medium_id);
      $items = $items->get();
      return $items;
   }

   public function getContactById($id){
      $items = contacts::where('id', $id)->first();
      return $items;
   }
   public function getContactHistoryById($id){
      $items = contact_histories::where('contact_id', $id)->get();
      return $items;
   }
   public function getContactFileById($id){
      $items = contact_files::where('contact_id', $id)->get();
      return $items;
   }

   public function getListMovies(){
      $items = movies::query();
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }
   public function getListMoviesDateByIds($ids)
   {
      $items = movies::selectRaw('EXTRACT(YEAR FROM created_at) AS year')
         ->selectRaw('EXTRACT(MONTH FROM created_at) AS month')
         ->selectRaw('EXTRACT(DAY FROM created_at) AS day')
         ->whereIn('id', $ids)
         ->groupBy('year', 'month', 'day')
         ->orderBy('year', 'DESC')
         ->orderBy('month')
         ->orderBy('day')
         ->get();

      return $items;
   }

   public function getMovieByDate($date){
      $createdAtFormatted = date('Y-m-d', strtotime($date));
      $items = movies::whereRaw("DATE(created_at) = ?", [$createdAtFormatted])
               ->orderBy('updated_at', 'DESC')
               ->get();
      return $items;
   }

   function getListMasterContactCategoryDetail($contact_category_id){
      $items = master_contact_category_details::where('contact_category_id',$contact_category_id);
      $items = $items->OrderBy("id", "asc")->get();
      return $items;
   }

}
