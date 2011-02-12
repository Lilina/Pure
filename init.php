<?php

function pure_do_item() {
	global $item;
	$timestamp = get_the_time();
	$midnight = (time() - (time() % 86400));

	$content = '
					<li class="feed-' . get_the_feed_id() .' entry" id="item-' . get_the_id() . '">
						<div class="collapsed">
							<div class="entry-date">';
	if ($timestamp < $midnight) {
		$content .= date('M n, Y', $timestamp);
	}
	else {
		$content .= date('g:i A', $timestamp);
	}
	$content .= '</div>
							<div class="entry-main">
								<span class="entry-source-title">' . get_the_feed_name() . '</span>
								<div class="entry-secondary">
									<h2 class="entry-title" id="title-' . get_the_id() . '">' . get_the_title() . '</h2>
									<div class="entry-secondary-snippet" id="content-' . get_the_id() . '">
										<span class="snippet">' . get_the_summary(200) . '</span>
									</div>
								</div>
							</div>
						</div>
						<div class="entry-container">
							<div class="entry-main">
								<h2 class="entry-title" id="title-' . get_the_id() . '">
									<a class="entry-title-link" href="' . get_the_link() . '">' . get_the_title() . '</a>
								</h2>
								<div class="entry-body">
									<div class="item-body">' . get_the_content() . '</div>';
	$metadata = enclosure_metadata();
	if ($item->metadata->enclosure) {
		$type = 'media';
		$filename = parse_url($item->metadata->enclosure, PHP_URL_PATH);
		$filename = basename($filename);
		if(!empty($metadata->type)) {
			list($type, ) = explode('/', $metadata->type, 2);
		}
		if ($type === 'image' || $type === 'video') {
			$content .= '<div class="player">';
			if ($type === 'audio') {
				$content .= '<audio src="' . $item->metadata->enclosure . '"></audio>';
			}
			elseif ($type === 'video') {
				$content .= '<video src="' . $item->metadata->enclosure . '" width="480" height="270"></video>';
			}

			$content .= '
				<div class="view-enclosure-parent">
					<a href="' . $item->metadata->enclosure . '" target="_blank">
						<span>Original ' . $type . ' source (' . $filename .')</span>
					</a>
				</div>
			</div>';
		}
	}
	$content .= '</div>
						</div>
						<div class="entry-actions">';
	$services = Services::get_for_item($item);

	if(!empty($services)) {
		$num = 0;
		foreach ($services as $id => $service) {
			$content .= '<span class="link"><a href="' . $service['action'] .
				'" class="service service-'. $service['type'] . ' service-' . $id . '"';

			if (!empty($service['icon']))
				$content .= 'style="background-image: url(\'' . $service['icon'] . '\')"';

			$content .= '>' . $service['label'];
			if ($num < 10) {
				$num++;
				if ($num === 10)
					$content .= ' [0]';
				else
					$content .= ' [' . $num . ']';
			}
			$content .= '</a></span>';
		}
	}
	?>
	<?php
	$content .= '</div></li>';
	return $content;
}


function pure_register(&$controller) {
	$controller->registerMethod('pure.loadItems', 'pure_multiple');
}
add_action('LilinaAPI-register', 'pure_register', 10, 1);

function pure_multiple($start = 0, $limit = 10, $conditions = array()) {
	$ids = LilinaAPI::items_getList($start, $limit, $conditions);
	global $item;
	$items = array();
	foreach ($ids as $id) {
		$item = $id;
		$items[$id->hash] = pure_do_item();
	}

	return $items;
}