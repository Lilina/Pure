/* From GitHub's jquery.hotkeys.js */
(function ($) {
	$.hotkeys = function (c) {
		for (key in c) $.hotkey(key, c[key]);
		return this
	};
	$.hotkey = function (c, d) {
		c = $.hotkeys.special[c] == null ? c.charCodeAt(0) : $.hotkeys.special[c];
		$.hotkeys.cache[c] = d;
		return this
	};
	$.hotkeys.cache = {};
	$.hotkeys.special = {
		enter: 45,
		"?": 191,
		"/": 223,
		"\\": 252,
		"`": 224,
		" ": 64
	};
	$.hotkeys.onNext = null;
	if ($.browser.mozilla && navigator.userAgent.indexOf('Macintosh') != -1) $.hotkeys.special["?"] = 0
})(jQuery);
jQuery(document).ready(function (a) {
	$("a[hotkey]").each(function () {
		$.hotkey($(this).attr("hotkey"), $(this).attr("href"))
	});
	$(document).bind("keydown.hotkey", function (c) {
		if (!$(c.target).is(":input")) {
			if (c.ctrlKey || c.altKey || c.metaKey) return true;
			c = c.shiftKey ? c.keyCode : c.keyCode + 32;

			if ($.hotkeys.onNext) {
				$.hotkeys.onNext(c);
				$.hotkeys.onNext = null;
				return false;
			}
			if (c = $.hotkeys.cache[c]) {
				$.isFunction(c) ? c.call(this) : (window.location = c);
				return false
			}
		}
	})
});

jQuery.fn.childrenHeight = function () {
	var height = 0;
	this.children().each(function() {
		height = height + $(this).outerHeight(false);
	});
	return height;
};

Pure = {};
Pure.items = 0;
Pure.mediaElementOptions = {
	defaultVideoWidth: 480,
	defaultVideoHeight: 270,
	videoWidth: -1,
	videoHeight: -1,
	audioWidth: 400,
	audioHeight: 30,
	enableAutosize: true,
	features: ['playpause','progress','current','duration','tracks','volume','fullscreen']
};
Pure.view = '';
Pure.subview = '';
Pure.conditions = {};
Pure.changingHash = false;
Pure.fitToWindow = function () {
	$('#main').css({
		height: $(window).height() - $('#navigation').outerHeight()
	});
	/*$('#sidebar').css({
		height: $(window).height() - $('#navigation').outerHeight()
	})*/
	$('#sub-tree').css({
		height: $(window).height() - $('#navigation').outerHeight() - $('#sidebar .lhn-section-footer').outerHeight() - $('#sidebar .lhn-section-primary').outerHeight()
	});
	$('#entries').css({
		height: $(window).height() - $('#navigation').outerHeight() - $('#chrome-header').outerHeight()
	});
};
Pure.bottomOnScreen = function (elem, parent) {
	var pos = $(elem).position().top;
	var parentHeight = $(parent).innerHeight();
	var height = $(elem).outerHeight();
	if ((pos + height) < 0) {
		return false;
	}
	if ((pos + height) > parentHeight) {
		return false;
	}
	return true;
};
Pure.scrollToTop = function (elem, parent) {
	if (!$(elem).position())
		return;

	var pos = $(parent).scrollTop() + $(elem).position().top;
	$(parent).stop().animate({scrollTop: pos}, 400);
};
Pure.setView = function (view) {
	Pure.view = view;
	Pure.subview = '';
	// Disabled for now
	if (false && history.pushState) {
		history.pushState({view: Pure.view, subview: Pure.subview}, "", Pure.view);
	}
	else {
		Pure.changingHash = true;
		window.location.hash = '!/' + Pure.view;
	}
};
Pure.setSubView = function (subview) {
	Pure.subview = subview;
	if (!Pure.subview)
		loc = Pure.view;
	else
		loc = Pure.view + '/' + Pure.subview;

	// Disabled for now
	if (false && history.pushState) {
		history.pushState({view: Pure.view, subview: Pure.subview}, "", loc);
	}
	else {
		Pure.changingHash = true;
		window.location.hash = '!/' + loc;
	}
};
Pure.loadFromState = function (state) {
	if (typeof state == "string") {
		var bits = state.replace(/^#!\//i, "").split("/");
		state = {};
		state.view = bits.shift();
		if (state.view == "feed")
			state.view += "/" + bits.shift();
		state.subview = bits.join("/");
	}
	if (!state)
		state = {view: Pure.view, subview: Pure.subview};

	if (Pure.view != state.view) {
		Pure.view = state.view;
		var bits = Pure.view.split("/");
		if (Pure.callbacks[bits[0]]) {
			Pure.callbacks[bits[0]].call(null, bits[1]);
		}
	}
	Pure.subview = state.subview;
};
Pure.beginLoading = function () {
	$('#loading-area').removeClass('hidden');
};
Pure.doneLoading = function () {
	$('#loading-area').addClass('hidden');
};

/**#@+
 * Delegates
 */
Pure.selectPrevious = function () {
	var item = Pure.UI.current.selectPrevious();
	Pure.setSubView('entry/' + item);
};
Pure.selectNext = function () {
	var item = Pure.UI.current.selectNext();
	Pure.setSubView('entry/' + item);
};
Pure.entryClick = function () {
	var item = Pure.UI.current.handlers.entryClick(this);
	if (!item)
		Pure.setSubView(false);
	else
		Pure.setSubView('entry/' + item);
	return false;
}
/**#@-*/

Pure.loadFeed = function (feed) {
	Pure.UI.current.loadFeed(feed);
	$('#entries').unbind('scroll.infscroll');
	$('#entries').empty();
	Pure.items = 0;
	Pure.conditions = {'feed': feed}
	LilinaAPI.call('pure.loadItems', {'start': Pure.items, 'conditions': Pure.conditions}, Pure.loadedItems, false, false, Pure.baseURL);
	Pure.beginLoading();
	Pure.setView('feed/' + feed);
	return false;
};
Pure.sidebarItemClick =function () {
	var feed = $(this).data('feed-id');
	return Pure.loadFeed(feed);
};

Pure.onScroll = function () {
	var pixelsLeft = Pure.UI.entryHeight - $('#entries').scrollTop() - $('#entries').height();
	if (pixelsLeft < 200) {
		Pure.loadMore();
	}
};
Pure.loadMore = function () {
	$('#entries').unbind('scroll.infscroll');
	Pure.items = Pure.items + 10;
	LilinaAPI.call('pure.loadItems', {'start': Pure.items, 'conditions': Pure.conditions}, Pure.loadedItems, false, false, Pure.baseURL);
	Pure.beginLoading();
};
Pure.loadDefault = function () {
	$('.tree-link-selected').removeClass('tree-link-selected');
	$('#entries').empty();
	Pure.items = 0;
	Pure.conditions = {};
	LilinaAPI.call('pure.loadItems', {'start': Pure.items, 'conditions': Pure.conditions}, Pure.loadedItems, false, false, Pure.baseURL);
	Pure.beginLoading();
	Pure.setView('reading-list');
	return false;
};
Pure.loadedItems = function (list) {
	$.each(list, function(id, item) {
		$('#entries').append(item);
	});
	$('video, audio').mediaelementplayer(Pure.mediaElementOptions);
	Pure.UI.entryHeight = $('#entries').childrenHeight();
	$('#entries').bind('scroll.infscroll', Pure.onScroll);
	Pure.doneLoading();
};
Pure.reloadItems = function (list) {
	$('#entries').unbind('scroll.infscroll').empty();
	Pure.items = 0;
	LilinaAPI.call('pure.loadItems', {'start': Pure.items, 'conditions': Pure.conditions}, Pure.loadedItems, false, false, Pure.baseURL);
	Pure.beginLoading();
};
Pure.callbacks = {
	"reading-list": Pure.loadDefault,
	"feed": Pure.loadFeed,
};

jQuery(document).ready(function($) {
	$(window).resize(Pure.fitToWindow);
	Pure.fitToWindow();

	//Pure.setView('reading-list');
	$(".entry .collapsed").live("click", Pure.entryClick);
	$('video,audio').mediaelementplayer(Pure.mediaElementOptions);
	$.hotkeys({
		"o": function () {
			var item = Pure.UI.current.handlers.entryClick($('.current-entry .collapsed'));
			Pure.setSubView('entry/' + item);
		},
		"j": Pure.selectPrevious,
		"k": Pure.selectNext,
		"v": function () {
			var newWindow = window.open($('.current-entry .entry-title-link').attr('href'));
			if(!newWindow)
				alert('Disable your popup blocker to view links.');
		},
		"s": function () {
			$.hotkeys.onNext = function (key) {
				if (key > 79 && key < 90) {
					var num = key - 80;
					if (num === 0)
						num = 10;
					var target = $(".current-entry .entry-actions .link:nth-child(" + num + ") a");
					if (target) {
						var newWindow = window.open($(target).attr('href'));
						if(!newWindow)
							alert('Disable your popup blocker to view links.');
					}
				}
			}
		},
		"g": function () {
			$.hotkeys.onNext = function (key) {
				switch (key) {
					// h
					case 104:

						break;
					// a
					case 97:
						Pure.loadDefault();
						break;
					default:
						console.log(key);
				}
				return;
			}
		},
		"r": Pure.reloadItems,
		"n": Pure.UI.current.goNext,
		" ": function () {
			if (Pure.bottomOnScreen($('.current-entry'), $('#entries'))) {
				Pure.selectNext();
			}
			else {
				var pos = $('#entries').scrollTop() + ($('#entries').innerHeight() * 0.75);
				$('#entries').stop().animate({scrollTop: pos}, 400);
			}
		}
	});
	Pure.UI.entryHeight = $('#entries').childrenHeight();
	$('#entries').bind('scroll.infscroll', Pure.onScroll);

	// State changing stuff
	$('#sidebar .link').click(Pure.sidebarItemClick);
	$('#overview-selector a').click(function () {
		$('.tree-link-selected').removeClass('tree-link-selected');
		$('#entries').empty();
		return false;
	});
	$('#reading-list-selector a').click(Pure.loadDefault);
	$(window).bind("hashchange", function(event) {
		if (Pure.changingHash) {
			Pure.changingHash = false;
			return;
		}
		var hash = window.location.hash;
		Pure.loadFromState(hash);
	});
	$('#viewer-refresh').click(Pure.reloadItems);
});

Pure.UI = {};
Pure.UI.List = {};
Pure.UI.List.selectPrevious = function () {
	$('#entries .expanded').removeClass('expanded');
	$('.current-entry').removeClass('current-entry')
		.prev().toggleClass('expanded').addClass('current-entry');
	
	Pure.scrollToTop($('.current-entry'), $('#entries'));
	return $('.current-entry').attr('id');
};
Pure.UI.List.selectNext = function () {
	$('#entries .expanded').removeClass('expanded');
	$('.current-entry').removeClass('current-entry')
		.next().toggleClass('expanded').addClass('current-entry');

	Pure.scrollToTop($('.current-entry'), $('#entries'));
	return $('.current-entry').attr('id');
};
Pure.UI.List.goNext = function () {
	$('.current-entry').removeClass('current-entry')
		.next().toggleClass('expanded').addClass('current-entry');

	Pure.scrollToTop($('.current-entry'), $('#entries'));
	return $('.current-entry').attr('id');
};
Pure.UI.List.loadFeed = function (id) {
	$('.tree-link-selected').removeClass('tree-link-selected');
	$('#sidebar .link[data-feed-id="' + id + '"]').addClass('tree-link-selected');
};
Pure.UI.List.handlers = {};
Pure.UI.List.handlers.entryClick = function (target) {
	if ($(target).parent().hasClass('expanded')) {
		$(target).parent().removeClass('expanded');
		return false;
	}
	$('#entries .expanded').removeClass('expanded');
	$('.current-entry').removeClass('current-entry');
	$(target).parent().toggleClass('expanded').addClass('current-entry');

	Pure.scrollToTop($('.current-entry'), $('#entries'));
	return $('.current-entry').attr('id');
};
Pure.UI.current = Pure.UI.List;