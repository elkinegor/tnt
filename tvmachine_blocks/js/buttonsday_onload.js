(function ($) {
	$(document).ready(function () {
		setButtonDayActive(window.tvmachine['day'], window.tvmachine['day1_j'], window.tvmachine['day2_j'], window.tvmachine['day3_j'], window.tvmachine['day01_j'], window.tvmachine['day02_j']);
		changeHour(window.tvmachine['template'],window.tvmachine['hour']);
	});
})(jQuery);