<?php
namespace App\Repositories\Interfaces;

interface MapRepositoryInterface
{   
    function getListKiroposts();
    function getTableLabels($officeId='',$roadTypeId='',$roadNo='',$upAndDownId='',$columnSort='',$optionSort='',$pagination=20);
    function getTableLandmark($landmarkTypeId='',$label='',$columnSort='',$optionSort='',$pagination=20);
    function getTableFacility($facilityTypeId='',$officeId='',$roadTypeId='',$roadNo='',$facilityName='',$columnSort='',$optionSort='',$pagination=20);
    function getTableContact($contactTypeId='',$contactCategoryId='',$report='',$createdAt='',$columnSort='',$optionSort='',$pagination=20);
    function getListFacility($listId);
    function getListFacilityByMenu($menu_medium_id);
    function getFacilityAttribute($id);
    function getFacilityFilesById($id);
    function getFacilityUrlsById($id);
    function getListLandmarks();
    function getListMoviesDate();
    function getListMovie($listDate);
    function getMovieById($id);
    function getMovieByDate($date);
    function getMenuLarges();
    function getMenuMedia($menu_large_id);
    function getMenuSmalls($menu_medium_id);
    function getMenuSmallsFacility($menu_medium_id);
    function getContactById($id);
    function getContactHistoryById($id);
    function getContactFileById($id);
    function getContactMaxId();
    function getListMovies();
    function getListMoviesDateByIds($ids);
    function getListMasterContactCategoryDetail($contact_category_id);



    //Master
    function getListMasterFacilityTypes();
    function getListMasterOffice();
    function getListMasterRoadTypes();
    function getListMasterUpAndDowns();
    function getListMasterLandmarkTypes();
    function getListMasterContactTypes();
    function getListMasterContactCategories();
    function getListMasterContactWay();
    
}