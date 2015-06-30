<?php

class ExhibitionHolder extends Page {

	private static $db = array(

	);

	private static $has_one = array(

	);

	

	private static $allowed_children = array('ExhibitionPage', 'ExhibitionHolder', 'RedirectorPage');

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName("Credit");
		return $fields;

	}
	public function ArchiveYears(){
		$exhibitions = ExhibitionPage::get();
		$exhibitionYears = new ArrayList();

	
		foreach($exhibitions as $exhibition){
			$exhibitionYear = new DataObject; 
			$exhibitionDateTime = $exhibition->obj("StartDate");

			if($exhibitionDateTime->Year()){

				$exhibitionYear->Year = $exhibitionDateTime->Year();
				$exhibitionYear->Link = $this->Link("year/".$exhibitionYear->Year);

				$exhibitionYears->push($exhibitionYear);
			}
		}

		//print_r($exhibitionYears);

		$exhibitionYears->removeDuplicates("Year");

		return $exhibitionYears;

	}

}

class ExhibitionHolder_Controller extends Page_Controller {
	private static $allowed_actions = array(
		'upcoming',
		'past',
		'year'
	);

	private static $url_handlers = array(
		'index' => 'current',
		'upcoming' => 'upcoming',
		'past' => 'past',
		'year//$Year' => 'year'

	);

	public function init() {
		parent::init();

	}

	public function index() {
		 $exhibitions = $this->Children()->sort('StartDate', 'ASC');
		 $paginatedList = new ArrayList();
		 $currentExhibitions = new PaginatedList($paginatedList, $this->request);
		 $currentExhibitions->setPageLength(10);
		foreach ($exhibitions as $exhibition) {
			if ($exhibition->obj("StartDate")->InPast() && $exhibition->obj("EndDate")->InFuture()) {
				$currentExhibitions->push($exhibition);
			} else if ((!$exhibition->StartDate) && (!$exhibition->EndDate)) {
				$currentExhibitions->push($exhibition);
			}
		}
		$Data = array(
			'ExhibitionList' => $currentExhibitions,
		);

		return $this->customise($Data)->renderWith(array('ExhibitionHolder', 'Page'));
	}

	public function upcoming() {
		 $exhibitions = $this->Children()->sort('StartDate', 'ASC');
		 $paginatedList = new ArrayList();
		 $upcomingExhibitions = new PaginatedList($paginatedList, $this->request);
		 $upcomingExhibitions->setPageLength(10);
		foreach ($exhibitions as $exhibition) {
			if ($exhibition->obj("StartDate")->InFuture()) {
				$upcomingExhibitions->push($exhibition);
			}
		}
		$Data = array(
			'ExhibitionList' => $upcomingExhibitions,
		);

		return $this->customise($Data)->renderWith(array('ExhibitionHolder', 'Page'));
	}

	public function past() {
		 $exhibitions = $this->Children()->sort('StartDate', 'DESC');		 
		 $paginatedList = new ArrayList();
		 $pastExhibitions = new PaginatedList($paginatedList, $this->request);
		 $pastExhibitions->setPageLength(10);
		foreach ($exhibitions as $exhibition) {
			if (($exhibition->EndDate && $exhibition->StartDate) && $exhibition->obj("EndDate")->InPast()) {
				$pastExhibitions->push($exhibition);
			}
		}
		$Data = array(
			'ExhibitionList' => $pastExhibitions,
		);

		return $this->customise($Data)->renderWith(array('ExhibitionHolder', 'Page'));
	}

	public function year() {

		if(!$this->getRequest()->param('Year')){
			return $this->redirect($this->Link());
		}

		$year = $this->getRequest()->param('Year');
		$yearFormatted = intval($year).'-01-01';

		$nextYear = $year + 1;
		$nextYearFormatted = $nextYear.'-01-01';

		$now = date('Y-m-d');

		 $exhibitions = ExhibitionPage::get()->filter(array(
		 	'StartDate:GreaterThanOrEqual' => $yearFormatted,
		 	'StartDate:LessThan' => $nextYearFormatted,
		 	'EndDate:LessThan' => $now
		 ))->sort('StartDate');

		 $paginatedList = new PaginatedList($exhibitions, $this->request);
		 $paginatedList->setPageLength(10);

		$Data = array(
			'ExhibitionList' => $paginatedList,
			'ActiveYear' => $year
		);

		return $this->customise($Data)->renderWith(array('ExhibitionHolder', 'Page'));
	}

}

?>