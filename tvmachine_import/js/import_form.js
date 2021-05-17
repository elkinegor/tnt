(function ($) {

	$(document).ready(function () {

		let tasks = [];
		let finish = false;
		let have_done = 0;


		$("#ftp_images_list .item").each(function(i,elem) {

			tasks.push(elem);

		});

		let total = tasks.length;

		if (tasks.length > 0) {
			for (var i = 0; i < 10; i++) {
			   process_task();
			}
		} else {
			console.log('No items');
	  	$("#edit-submit").click();
		}

		function process_task() {

			if (tasks.length > 0 && finish == false) {

				let elem = tasks.pop();

				$.ajax({
				  type: "POST",
				  url: "/ftp_task.php",
				  data: ({data : $(elem).text()}),
				  success: function(response){

				  	have_done++;

				  	$("#ftp_result .item-" + $(elem).data("id")).html("Response "+ response);

				  	console.log(response);

				  	$("#process_state").text('Have done: '+ have_done);
				  	process_task();

				  	console.log('Have done: '+ have_done);

				  }
				});

			} else {
				finish = true;
			}

			if (have_done == total) {
				console.log('Finish');
	  		$("#edit-submit").click();
	  	}
		}		

	});
})(jQuery);