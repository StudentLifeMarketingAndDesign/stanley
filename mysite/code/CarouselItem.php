<?php

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;

	class CarouselItem extends DataObject {

		private static $db = array(
			"Title" => "Varchar(155)",
			"SubTitle" => "Varchar(155)",
			"SortOrder"=>"Int",
			"ExternalLink" => "Text"
		);

		private static $has_one = array (
			"AssociatedPage" => SiteTree::class,
			'Image' => Image::class
		);

		// Summary fields
		private static $summary_fields = array(
			'Title' => 'Title',
			'SubTitle' => 'SubTitle'
		);

		private static $default_sort = "SortOrder";

		private static $owns = array('Image');


		public function getCMSFields() {
			$fields = new FieldList();

			$fields->push( new TextField( 'Title', 'Heading' ));
			$fields->push( new TextField( 'SubTitle', 'Subheading' ));
			$fields->push( new UploadField( 'Image', 'Image (1400px width, 600px height)' ));

			$fields->push( new TreeDropdownField("AssociatedPageID", "Link to this page", SiteTree::class));

			$fields->push( new TextField( 'ExternalLink', 'Use an external link instead (overrides above)' ));


			return $fields;
		}

	}
