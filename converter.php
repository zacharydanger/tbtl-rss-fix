<?php
$rss_feed = '/home/zcampbell/Desktop/tbtl_rss/tbtl.rss';

$feed = simplexml_load_string(file_get_contents($rss_feed));

$new_feed = new SimpleXMLElement('<rss></rss>');
$channel = $new_feed->addChild('channel');

function sanitize($string) {
	$replace = array('&nbsp;');
	$with = array(' ');
	return str_replace($replace, $with, $string);
}

function add_children(SimpleXMLElement $parent, SimpleXMLElement $child) {
	$element = $parent->addChild($child->getName(), sanitize($child));
	foreach($child->attributes as $key => $value) {
		$element->addAttribute($key, $value);
	}
	foreach($child->children() as $i => $grand_child) {
		add_children($element, $grand_child);
	}
}

$items = array();
foreach($feed->channel->children() as $child) {
	if('item' !== $child->getName()) {
		add_children($channel, $child);
	} else {
		$child_time = strtotime($child->pubDate);
		$child_date = date('Y-m-d', $child_time);
		$items[$child_date][] = $child;
	}
}

foreach($items as $date => $item_list) {
	if(3 == count($item_list)) {
		//$item_list = array_reverse($item_list);
		$start_hour = 19;

		$pub_time = strtotime($date);

		$date_string = date('D, d M Y', $pub_time);
		foreach($item_list as $i => $item) {
			$new_time = $date_string . ' ' . $start_hour . ':00:00 -0700';
			$item->pubDate = $new_time;
			//echo $item->title . "\t" . $item->pubDate . "\t" . $new_time . "\n";
			$start_hour++;
			add_children($channel, $item);
		}
	} else {
		foreach($item_list as $i => $item) {
			add_children($channel, $item);
		}
	}
}
$new_feed->addAttribute('version', '2.0');
echo $new_feed->asXML();
?>