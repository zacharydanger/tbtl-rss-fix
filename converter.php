<?php
$rss_feed = 'http://www.mynorthwest.com/rss/tbtl.rss';

$feed = simplexml_load_string(file_get_contents($rss_feed));

$items = array();
foreach($feed->channel->children() as $child) {
	if('item' == $child->getName()) {
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
			$start_hour++;
		}
	}
}

echo $feed->asXML();
?>