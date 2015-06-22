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

}

class ExhibitionHolder_Controller extends Page_Controller {
	private static $allowed_actions = array(
		'upcoming',
		'past',
	);

	private static $url_handlers = array(
		'index' => 'current',
		'upcoming' => 'upcoming',
		'past' => 'past',

	);

	public function init() {
		parent::init();

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

}

?>