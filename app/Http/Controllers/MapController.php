<?php

namespace App\Http\Controllers;

use App\Models\contact_files;
use App\Models\contact_histories;
use App\Models\contacts;
use App\Models\facilities;
use App\Repositories\Interfaces\MapRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class MapController extends StatusController
{
    /**
     * Index map
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private $mapRepository;

    public function __construct(MapRepositoryInterface $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    public function index()
    {
        return view("map.index");
    }

    public function loadLabelCesiumSearch(Request $request)
    {
        $officeId = $request->officeId;
        $roadTypeId = $request->roadTypeId;
        $roadNo = $request->roadNo;
        $upAndDownId = $request->upAndDownId;
        $columnSort = $request->columnSort;
        $optionSort = $request->optionSort;
        $tableLabels = $this->mapRepository->getTableLabels($officeId, $roadTypeId, $roadNo, $upAndDownId, $columnSort, $optionSort, 20);
        return view('map.table.table_label_filter', compact('tableLabels'))->render();
    }


    public function loadFacilitySearch(Request $request)
    {
        $facilityTypeId = $request->facilityTypeId;
        $facilityName = $request->facilityName;
        $officeId = $request->officeId;
        $roadTypeId = $request->roadTypeId;
        $roadNo = $request->roadNo;
        $upAndDownId = $request->upAndDownId;
        $columnSort = $request->columnSort;
        $optionSort = $request->optionSort;
        $tableFacility = $this->mapRepository->getTableFacility($facilityTypeId, $officeId, $roadTypeId, $roadNo, $facilityName, $columnSort, $optionSort, $pagination = 20);
        return view('map.table.table_facility_filter', compact('tableFacility'))->render();
    }

    public function loadContactSearch(Request $request)
    {
        $contactTypeId = $request->contactTypeId;
        $contactCategoryId = $request->contactCategoryId;
        $report = $request->report;
        $createdAt = $request->createdAt;
        $columnSort = $request->columnSort;
        $optionSort = $request->optionSort;
        $tableContact = $this->mapRepository->getTableContact($contactTypeId, $contactCategoryId, $report, $createdAt,  $columnSort, $optionSort, $pagination = 20);
        return view('map.table.table_contact_filter', compact('tableContact'))->render();
    }
    public function loadContactSearchCesium(Request $request)
    {
        $contactTypeId = $request->contactTypeId;
        $contactCategoryId = $request->contactCategoryId;
        $report = $request->report;
        $createdAt = $request->createdAt;
        $listContact = $this->mapRepository->getTableContact($contactTypeId, $contactCategoryId, $report, $createdAt,'','',0);
        return response()->json(
            ['listContact' => $listContact]
        );
    }

    public function loadContactDetail(Request $request)
    {
        $id = $request->id;
        $contact = $this->mapRepository->getContactById($id);
        $contactHistory = $this->mapRepository->getContactHistoryById($id);
        $contactFile = $this->mapRepository->getContactFileById($id);
        foreach ($contactFile as $file) {
            $file->name = basename($file->name_file);
            $filePath = public_path($file->name_file);
            $file->existFile = File::exists($filePath);
        }
        return response()->json(
            [
                'contact' => $contact,
                'contactHistory' => $contactHistory,
                'contactFile' => $contactFile,
            ]
        );
    }
    public function submitFormContact (Request $request){
        try {
            $formDataDetail1 = $request->formDataDetail1;
            parse_str($formDataDetail1, $params);
            $id = $params['id'];
            $params['reception_department_id'] = $params['reception_department_id'] == '' ? null : $params['reception_department_id'];
            $params['lat'] =  $params['lat'] != '' ? floatval($params['lat']): null;
            $params['lon'] =  $params['lon'] != '' ? floatval($params['lon']): null;
            $formDataDetail2 = json_decode($request->formDataDetail2,true);
            DB::beginTransaction();
            if($id == ''){
                $contactId = $this->mapRepository->getContactMaxId()->id;
                $mewContactId = $contactId ? $contactId + 1 : 1;
                $params['id'] = $mewContactId;
                contacts::create($params);  

                foreach ($formDataDetail2 as $index => $value) {
                    $data_insert_history = array(
                        'contact_id' => $mewContactId,
                        'receptionist' => $value['receptionist'],
                        'content' => $value['content'],
                    );
                    contact_histories::create($data_insert_history);
                }
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $fileName = $file->getClientOriginalName(); 
                        $file->move(public_path('files/').$mewContactId, $fileName); 
                        $filePath = 'files/' . $mewContactId . '/' . $fileName;
                        $data_insert_file= array(
                            'contact_id' => $mewContactId,
                            'name_file' => $filePath ,
                        );
                        contact_files::create($data_insert_file);
                    }
                }
            }else{
                // mode edit
                contacts::where("id", $id)->update($params);
                contact_histories::where('contact_id', '=', $id)->delete();
                foreach ($formDataDetail2 as $index => $value) {
                    $data_insert_history = array(
                        'contact_id' => $id,
                        'receptionist' => $value['receptionist'],
                        'content' => $value['content'],
                    );
                    contact_histories::create($data_insert_history);
                }
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {
                        $fileName = $file->getClientOriginalName(); 
                        $file->move(public_path('files/').$id, $fileName); 
                    }
                }
                contact_files::where('contact_id', '=', $id)->delete();
                $currentImages = $request->name_file;
                $decodedImages = [];
                foreach($currentImages as $file ){
                    if ($file !== 'undefined') {
                        $fileDecode = urldecode($file);
                        $decodedImages[] = $fileDecode;
                        $filePath = 'files/' . $id . '/' . $fileDecode;
                        $data_insert_file= array(
                            'contact_id' => $id,
                            'name_file' => $filePath ,
                        );
                        contact_files::create($data_insert_file);
                    }
                }
                $directory = public_path('files/' . $id);
                if (is_dir($directory)) {
                    $files = File::files($directory);
                    if(isset($files)){
                        foreach ($files as $file) {
                            $filename = pathinfo($file, PATHINFO_FILENAME);
                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                            $fullName = $filename . '.' . $extension;
                            if (!in_array($fullName, $decodedImages)) {
                                File::delete($file);
                            }
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'success'], 200);
        } catch (Exception $e ) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
            
        }
    }

    

    public function loadFacility(Request $request)
    {
        $listId = $request->listId;
        $listFacility = $this->mapRepository->getListFacility($listId);
        return response()->json(
            ['listFacility' => $listFacility]
        );
    }
    public function loadFacilityInfo(Request $request)
    {
        $id = $request->id;
        $facility = $this->mapRepository->getFacilityAttribute($id);
        $listFacilityUrl = $this->mapRepository->getFacilityUrlsById($id);
        $listFacilityFile = $this->mapRepository->getFacilityFilesById($id);
        return response()->json(
            [
                'facility' => $facility,
                'listFacilityUrl' => $listFacilityUrl,
                'listFacilityFile' => $listFacilityFile,
            ]
        );
    }

    public function loadLandmarkSearch(Request $request)
    {
        $landmarkTypeId = $request->landmarkTypeId;
        $label = $request->label;
        $columnSort = $request->columnSort;
        $optionSort = $request->optionSort;
        $tableLandmark = $this->mapRepository->getTableLandmark($landmarkTypeId, $label, $columnSort, $optionSort, 20);
        return view('map.table.table_landmark_filter', compact('tableLandmark'))->render();
    }

    public function groupMoviesDate($listDate)
    {
        $groupedRecords = [];
        foreach ($listDate as $movie) {
            $year = $movie->year;
            $month = $movie->month;
            $day = $movie->day;

            if (!isset($groupedRecords[$year])) {
                $groupedRecords[$year] = [];
            }
            if (!isset($groupedRecords[$year][$month])) {
                $groupedRecords[$year][$month] = [];
            }
            $groupedRecords[$year][$month][] = $day;
        }
        return $groupedRecords;
    }

    public function loadGroupMovie(Request $request) {
        $listDate = $request->listDate;
        $getListMovie = $this->mapRepository->getListMovie($listDate);
        return response()->json(
            ['getListMovie' => $getListMovie]
        );
    }

    public function loadMovie(Request $request) {
        $id = $request->id;
        $infoMovie = $this->mapRepository->getMovieById($id);
        return response()->json(
            ['infoMovie' => $infoMovie]
        );
    }

    public function getMenuMedia(Request $request) {
        $menu_large_id = $request->menu_large_id;
        $groupMenuMedia = $this->mapRepository->getMenuMedia($menu_large_id);
        return response()->json(
            ['groupMenuMedia' => $groupMenuMedia]
        );
    }

    public function getMenuSmalls(Request $request) {
        $menu_medium_id = $request->menu_medium_id;
        $groupMenuSmalls = $this->mapRepository->getMenuSmalls($menu_medium_id);
        return response()->json(
            ['groupMenuSmalls' => $groupMenuSmalls]
        );
    }

    public function loadFacilityByMenu(Request $request)
    {
        $listId = $request->listId;
        $listFacility = $this->mapRepository->getListFacilityByMenu($listId);
        return response()->json(
            ['listFacility' => $listFacility]
        );
    }
    public function getMenuSmallsFacility(Request $request)
    {
        $menu_medium_id = $request->menu_medium_id;
        $listMenuFacility = $this->mapRepository->getMenuSmallsFacility($menu_medium_id);
        return response()->json(
            ['listMenuFacility' => $listMenuFacility]
        );
    }
    
    public function downloadFile(Request $request){
        $name_file = $request->name_file;
        $name = basename($name_file);
        $filePath = public_path($name_file);
        if (file_exists($filePath)) {
            return response()->download($filePath,$name);
        } else {
            return response('error', 404);
        }
    }

    public function loadPopupMovie(Request $request){
        $latCurrent = $request->lat;
        $lonCurrent = $request->lon;
        $listMovies = $this->mapRepository->getListMovies();

        $distances = [];
        $distancesMeterCurrent = env('DISTANCE_METER') ? floatval(env('DISTANCE_METER') / 1000):0;
        foreach ($listMovies as $point) {
            $distance = $this->calculateDistance($latCurrent, $lonCurrent, $point['lat'], $point['lon']);
            if($distance && $distance <= $distancesMeterCurrent){
                $distances[$point['id']] = $distance;
            }
        }
        asort($distances);
        $nearestKeys = array_keys($distances);
        $listDate = $this->mapRepository->getListMoviesDateByIds($nearestKeys);
        return response()->json(
            ['listDate' => $listDate]
        );
    }

    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; 
    
        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);
    
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $earthRadius * $c; 
    
        return $distance;
    }
    public function loadMovieByDate(Request $request){
        $date = $request->date;
        $listMovie = $this->mapRepository->getMovieByDate($date);
        return response()->json(
            ['listMovie' => $listMovie]
        );
    }

    public function getListCategoryDetail(Request $request){
        $contact_category_id = $request->contact_category_id;
        $listMasterContactCategoryDetail = $this->mapRepository->getListMasterContactCategoryDetail($contact_category_id);
        return response()->json(
            ['listMasterContactCategoryDetail' => $listMasterContactCategoryDetail]
        );
    }

    public function submitFormLabel(Request $request){
        $formDataTab1 = $request->formDataTab1;
        $decodedFormDataTab1 = urldecode($formDataTab1);
        $jsonArray = json_decode($decodedFormDataTab1, true);
        $id = $request->id;
        DB::beginTransaction();
        try {
            facilities::where("id", $id)->update(['attribute'=>$jsonArray]);
            DB::commit();
            return response()->json(['message' => 'success'], 200);
        } catch (Exception $e ) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    

}
