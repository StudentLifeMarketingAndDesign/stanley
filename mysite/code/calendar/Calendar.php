<?php
use SilverStripe\ORM\DataList;
class Calendar extends Page {

	private static $allowed_children = array (
		'CalendarEvent'
	);

	private static $icon_class = 'font-icon-calendar';

	private static $timezone = "America/Chicago";
	private static $language = "EN";

	public function getEventList($start, $end, $filter = null, $limit = null, $announcement_filter = null) {
		$children = $this->AllChildren();
				$ids = $children->column('ID');
				$datetimeClass = 'CalendarDateTime';
				$relation = 'EventID';
				$eventClass = 'CalendarEvent';
				// $datetimeClass = $this->getDateTimeClass();
				// $relation = $this->getDateToEventRelation();
				// $eventClass = $this->getEventClass();
				$list = DataList::create($datetimeClass)
					->filter(array(
						$relation => $ids
					))
					->innerJoin($eventClass, "$relation = \"{$eventClass}\".\"ID\"")
					->innerJoin("SiteTree", "\"SiteTree\".\"ID\" = \"{$eventClass}\".\"ID\"")
					->where("Recursion != 1");
				if($start && $end) {
					$list = $list->where("
							(StartDate <= '$start' AND EndDate >= '$end') OR
							(StartDate BETWEEN '$start' AND '$end') OR
							(EndDate BETWEEN '$start' AND '$end')
							");
				}
				else if($start) {
					$list = $list->where("(StartDate >= '$start' OR EndDate > '$start')");
				}
				else if($end) {
					$list = $list->where("(EndDate <= '$end' OR StartDate < '$end')");
				}
				if($filter) {
					$list = $list->where($filter);
				}
				return $list;
			}
}
