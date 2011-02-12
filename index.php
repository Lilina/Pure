<?php
/**
 * Pure reader.
 * @author Ryan McCue <cubegames@gmail.com>
 * @author Na'Design
 */
/**
*/
header('Content-Type: text/html; charset=utf-8');

require_once(LILINA_INCPATH . '/core/auth-functions.php');

$user = new User();
$authenticated = !!$user->identify();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/1">

	<title><?php /*the_page_title();*/ template_sitename();?></title>

	<link rel="stylesheet" type="text/css" href="<?php template_directory(); ?>/style.css" media="screen"/>

	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<script src="<?php echo get_option('baseurl') ?>inc/js/jquery.js"></script>
	<script src="<?php echo get_option('baseurl') ?>inc/js/mediaelement/mediaelement-and-player.min.js"></script>
	<link rel="stylesheet" href="<?php echo get_option('baseurl') ?>inc/js/mediaelement/mediaelementplayer.css" />

<?php
	template_header();
?>

</head>
<body class="river-page">

<div id="navigation">
	<div class="user">
		<a href="<?php echo get_option('baseurl') ?>" class="site-name"><?php template_sitename() ?></a>
<?php
if($authenticated) {
?>
		<a href="<?php echo get_option('baseurl') ?>admin/">Settings</a>
		<a href="#">Help</a>
		<a href="<?php echo get_option('baseurl') ?>admin/login.php?logout=logout&amp;return=">Sign out</a>
<?php
}
else {
?>
		<a href="<?php echo get_option('baseurl') ?>admin/login.php?return=index.php">Login</a>
<?php
}
?>
	</div>
</div>

<div class="message-area hidden" id="loading-area" style="margin-left: -27px; width: 64px; "><div class="message-area-inner message-area-text-container"><span id="loading-area-text" class="message-area-text">Loading...</span></div>
<div class="message-area-inner message-area-bottom-1"></div>
<div class="message-area-inner message-area-bottom-2"></div>
<div class="message-area-inner message-area-bottom-3"></div></div>

<div id="main">
	<div id="wrapper">
		<div id="chrome">
			<div id="chrome-header">
				<span id="chrome-view-links">Show:
					<span class="unselectable link" id="view-cards">Expanded</span> -
					<span class="unselectable link link-selected" id="view-list">List</span>
				</span>
				<div role="wairole:button" tabindex="0" class="goog-button goog-button-base unselectable goog-inline-block goog-button-float-left goog-menu-button hidden" id="chrome-lhn-menu">
					<div class="goog-button-base-outer-box goog-inline-block">
						<div class="goog-button-base-inner-box goog-inline-block">
							<div class="goog-button-base-pos">
								<div class="goog-button-base-top-shadow">&nbsp;</div>
								<div class="goog-button-base-content">
									<div class="goog-button-body">Navigation</div>
									<div class="goog-menu-button-dropdown"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="viewer-header">
					<div id="viewer-top-controls">
						<div id="viewer-all-new-links">Show:
							<span class="unselectable link" id="show-new">a lot of new items</span> -
							<span class="unselectable link link-selected" id="show-all">all items</span>
						</div>
						<div id="mark-all-as-read-split-button">
							<div role="wairole:button" tabindex="0" class="goog-button goog-button-base unselectable goog-inline-block goog-button-float-left goog-button-tight viewer-buttons" id="mark-all-as-read">
								<div class="goog-button-base-outer-box goog-inline-block">
									<div class="goog-button-base-inner-box goog-inline-block">
										<div class="goog-button-base-pos">
											<div class="goog-button-base-top-shadow">&nbsp;</div>
											<div class="goog-button-base-content">
												<div class="goog-button-body">Mark all as read</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div role="wairole:button" tabindex="0" class="goog-button goog-button-base unselectable goog-inline-block goog-button-float-left goog-menu-button goog-button-tight viewer-buttons" id="mark-all-as-read-options"><div class="goog-button-base-outer-box goog-inline-block"><div class="goog-button-base-inner-box goog-inline-block"><div class="goog-button-base-pos"><div class="goog-button-base-top-shadow">&nbsp;</div><div class="goog-button-base-content"><div class="goog-button-body">&nbsp;</div><div class="goog-menu-button-dropdown"></div></div></div></div></div></div></div>

							<div role="wairole:button" tabindex="0" class="goog-button goog-button-base unselectable goog-inline-block goog-button-float-left goog-button-tight viewer-buttons" id="viewer-refresh">
								<div class="goog-button-base-outer-box goog-inline-block">
									<div class="goog-button-base-inner-box goog-inline-block">
										<div class="goog-button-base-pos">
											<div class="goog-button-base-top-shadow">&nbsp;</div>
											<div class="goog-button-base-content">
												<div class="goog-button-body">Refresh</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div role="wairole:button" tabindex="0" class="goog-button goog-button-base unselectable goog-inline-block goog-button-float-left goog-menu-button goog-button-tight" id="stream-prefs-menu"><div class="goog-button-base-outer-box goog-inline-block"><div class="goog-button-base-inner-box goog-inline-block"><div class="goog-button-base-pos"><div class="goog-button-base-top-shadow">&nbsp;</div><div class="goog-button-base-content"><div class="goog-button-body">Feed settings...</div><div class="goog-menu-button-dropdown"></div></div></div></div></div></div>
							<span id="viewer-single-parent"></span>
							<span id="viewer-single-item-parent"></span>
						</div>
						<div id="quick-add-success" class="hidden">You have subscribed to "<span id="quick-add-success-title">CNN.com</span>."</div>
						<div id="viewer-details" class="hidden"><div class="viewer-details-stats-container"></div>
				<div class="viewer-details-charts"><div class="tab-group"><div id="sub-day-bucket-chart-header" class="unselectable tab-header tab-header-selected">Last 30 days</div>
				<div id="sub-hour-bucket-chart-header" class="unselectable tab-header">Time of day</div>
				<div id="sub-dow-bucket-chart-header" class="unselectable tab-header">Day of the week</div>



				<div class="tab-group-contents"><div id="sub-day-bucket-chart-contents" class="tab-contents"><table class="sub-day-bucket-display"><tbody><tr><td align="left"><map id="sub-day-bucket-map" name="sub-day-bucket-map"></map>
				<input type="hidden" class="chart-data" value=""></td></tr></tbody></table>
				<div class="bucket-chart-legend"><div class="published"><div class="color-box"></div>
				<div class="label">Items posted</div></div>
				<div class="read"><div class="color-box"></div>
				<div class="label">Items read</div></div></div></div>
				<div id="sub-hour-bucket-chart-contents" class="tab-contents hidden"><table class="sub-hour-bucket-display"><tbody><tr><td align="left"><map id="sub-hour-bucket-map" name="sub-hour-bucket-map"></map>
				<input type="hidden" class="chart-data" value=""></td></tr></tbody></table>
				<div class="bucket-chart-legend"><div class="published"><div class="color-box"></div>
				<div class="label">Items posted</div></div>
				<div class="read"><div class="color-box"></div>
				<div class="label">Items read</div></div></div></div>
				<div id="sub-dow-bucket-chart-contents" class="tab-contents hidden"><table class="sub-dow-bucket-display"><tbody><tr><td align="left"><map id="sub-dow-bucket-map" name="sub-dow-bucket-map"></map>
				<input type="hidden" class="chart-data" value=""></td></tr></tbody></table>
				<div class="bucket-chart-legend"><div class="published"><div class="color-box"></div>
				<div class="label">Items posted</div></div>
				<div class="read"><div class="color-box"></div>
				<div class="label">Items read</div></div></div></div>


				</div></div></div></div></div>
			</div>
			<div id="chrome-viewer-container" class="samedir">
				<ul id="entries" class="list">
<?php
// We call it with false as a parameter to avoid incrementing the item number
if(has_items()) {
		while(has_items(array('limit' => 10))):
			the_item();
			echo pure_do_item();
		endwhile;
}
elseif(!has_feeds()) {
?>
					<li>No feeds!</li>
<?php
}
else {
?>
					<li>No items!</li>
<?php
}
?>
				</ul>
			</div>
		</div>
		<div id="nav">
			<div id="lhn-selectors" class="section lhn-section  ">
				<div class="lhn-section-secondary"><div id="overview-selector" class="selector"><a href="#!/overview-page" class="link"><span class="text">Home</span></a></div></div>
				<div class="lhn-section-primary">
					<div id="reading-list-selector" class="selector unread">
						<a href="#!/reading-list" class="link"><span class="text">All items</span></a>
					</div>
				</div>
				<div class="lhn-section-secondary">
					<div id="star-selector" class="selector">
					<a href="#!/starred" class="link">
						<span class="text">Starred items</span>
						<div class="selector-icon"></div>
					</a>
				</div>
				<div id="your-items-tree-container" class="scroll-tree-container">
					<ul id="your-items-tree" class="scroll-tree">
						<li class="folder unselectable expanded" id="your-items-tree-item-0-main">
							<ul>
								<li class="broadcast unselectable expanded" id="your-items-tree-item-1-main">
									<a class="link" href="#!/broadcast" id="your-items-tree-item-1-link">
										<span class="name broadcast-name name-d-1" id="your-items-tree-item-1-name" title="Shared items">
											<span class="unread-count broadcast-unread-count unread-count-d-1" id="your-items-tree-item-1-unread-count"></span>
											<span class="name-text broadcast-name-text name-text-d-1">Shared items</span>
										</span>
									</a>
								</li>
								<li class="created unselectable expanded" id="your-items-tree-item-2-main">
									<a class="link" href="#!/created" id="your-items-tree-item-2-link">
										<span class="name created-name name-d-1" id="your-items-tree-item-2-name" title="Notes">
											<span class="unread-count created-unread-count unread-count-d-1" id="your-items-tree-item-2-unread-count">
											</span>
											<span class="name-text created-name-text name-text-d-1">Notes</span>
										</span>
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
			<div id="sidebar">
				<div id="lhn-subscriptions-menubutton" class="section-button section-menubutton goog-menu-button goog-inline-block" role="button" style="-webkit-user-select: none; " tabindex="0" aria-haspopup="true" aria-pressed="false" aria-expanded="false" aria-activedescendant=":g"></div>
				<div class="lhn-section-primary"><span id="sub-tree-header" class="unselectable">Subscriptions</span><span id="sub-tree-refreshing" class="hidden">refreshing...</span></div>
				<ul id="sub-tree" class="scroll-tree">
					<li class="folder expanded">
					<?php if( has_feeds() ): ?>
						<ul>
							<?php list_feeds(array(
								'title_length' => 35,
								'format' => '
							<li class="sub expanded">
								<a href="#%1$s" data-feed-id="%5$s" class="link tree-link-selected-">
									<span class="icon sub-icon favicon" style="background-image: url(%2$s)">&nbsp;</span>
									<span class="name sub-name"><span class="name-text">%3$s</span></span>
								</a>
							</li>')); ?>
						</ul>
					<?php endif; ?>
					</li>
				</ul>
				<div class="lhn-section-footer"><a href="<?php echo get_option('baseurl') ?>admin/feeds.php" id="sub-tree-subscriptions">Manage subscriptions »</a></div>
			</div>
		</div>
	</div>
</div>

<?php template_footer(); ?>
<!-- Generated in: <?php global $timer_start; echo lilina_timer_end($timer_start); ?> -->

<script type="text/javascript" src="<?php echo get_option('baseurl') ?>inc/js/api.js"></script>
<script src="<?php template_directory(); ?>/pure.js"></script>
<script>
	Pure.baseURL = '<?php echo get_option('baseurl') ?>';
</script>
</body>
</html>