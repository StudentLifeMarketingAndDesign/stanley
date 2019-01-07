<?php
class StaffPage extends Page {

	private static $db = array(
		"FirstName"      => "Text",
		"LastName"       => "Text",
		"Position"       => "Text",
		"EmailAddress"   => "Text",
		"Phone"          => "Text",
		
	);

	private static $has_one = array(
		"Photo" => "Image",
	);

	private static $belongs_many_many = array(
		"Teams" => "StaffTeam",
	);
	private static $defaults = array(
		'Content' => ''
	);
	public function getCMSFields() {
		SiteTree::disableCMSFieldsExtensions();
		$fields = parent::getCMSFields();
		SiteTree::enableCMSFieldsExtensions();

		$fields->removeByName("Content");
		$fields->removeByName("Metadata");

		$fields->addFieldToTab("Root.Main", new TextField("FirstName", "First Name"));
		$fields->addFieldToTab("Root.Main", new TextField("LastName", "Last Name"));
		$fields->addFieldToTab("Root.Main", new TextField("Position", "Position"));
		$fields->addFieldToTab("Root.Main", new TextField("EmailAddress", "Email address"));
		$fields->addFieldToTab("Root.Main", new TextField("Phone", "Phone (XXX-XXX-XXXX)"));
	
		$fields->addFieldToTab("Root.Main", new CheckboxSetField("Teams", 'Team', StaffTeam::get()->map('ID', 'Name')));

		//$fields->addFieldToTab("Root.Main", new LiteralField("TeamLabel", ''));

		$fields->addFieldToTab("Root.Main", new HTMLEditorField("Content", "Biography"));
		$fields->addFieldToTab("Root.Main", new UploadField("Photo", "Photo (4:3 preferred - resizes to 760 x 507)"));
		$fields->addFieldToTab("Root.Main", new HTMLEditorField("Content", "Biography"));

		$this->extend('updateCMSFields', $fields);
		$fields->removeByName("BackgroundImage");
		return $fields;

	}
	public function FullNameTruncated() {
		$lastName = $this->owner->LastName;
		$fullName = $this->owner->FirstName.' '.substr($lastName, 0, 1).'.';

		return $fullName;
	}
	//private static $allowed_children = array("");

}
class StaffPage_Controller extends Page_Controller {

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private static $allowed_actions = array(
	);

	public function init() {
		parent::init();

	}
	public function NewsPosts() {

		$memberId = $this->EmailAddress;

		if (isset($memberId)) {
			$url = 'http://studentlife.uiowa.edu/news/rss?member='.$memberId;
			return $this->RSSDisplay(20, $url);
		} else {
			return false;
		}

	}
}