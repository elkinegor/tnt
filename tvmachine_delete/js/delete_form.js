(function ($) {

	$(document).ready(function () {


		function open_new_tab(url, id) {
			//Open new tabs
			//window.open(url, '_blank');
			//console.log(url);

			// Open popup
			window.open(url,id,'height=500,width=600');

			// Alternative for openning new tabs
			/*Object.assign(document.createElement('a'), {
			  target: '_blank',
			  url,
			}).click();*/
		}

		function open_links(day_to_open) {

			var current_date = new Date();
			current_date.setDate(new Date().getDate()+day_to_open);

			var day = current_date.getDate();
			var month = current_date.getMonth()+1;


			var linksArray1 = ['1','3','6','9','12','15','18','21'];
			var linksArray2 = ['1','2','4','6','8','10','12','14','16','18','20','22'];
			var linksArray3 = ['1','2','4','6','8','10','12','14','16','18','20','22'];

			for (var i = 0; i < linksArray1.length; i++) {
			    
			    var url_to_open = 'https://www.tvguia.es/program/content/6/1/'+month+'/'+day+'/'+linksArray1[i]+'/0/FFFFFF/FFFFFF/313131/111111';
			    window.setTimeout(open_new_tab, 6000*(i+1), url_to_open, "mywindow1_"+i);
			}

			for (var i = 0; i < linksArray2.length; i++) {
			    
			    var url_to_open = 'https://www.tvguia.es/program/content/5/1/'+month+'/'+day+'/'+linksArray2[i]+'/0/FFFFFF/FFFFFF/313131/111111';
			    window.setTimeout(open_new_tab, 5000*(i+1), url_to_open, "mywindow2_"+i);
			}

			for (var i = 0; i < linksArray3.length; i++) {
			    
			    var url_to_open = 'https://www.tvguia.es/program/content/9/1/'+month+'/'+day+'/'+linksArray3[i]+'/0/FFFFFF/FFFFFF/313131/111111';
			    window.setTimeout(open_new_tab, 5000*(i+1), url_to_open, "mywindow3_"+i);
			}
		}	

		$('#edit-warm-day-02').click(function(){
			open_links(-2);
			return false;
		});
		$('#edit-warm-day-01').click(function(){
			open_links(-1);
			return false;
		});
		$('#edit-warm-day-0').click(function(){
			open_links(0);
			return false;
		});
		$('#edit-warm-day-1').click(function(){
			open_links(1);
			return false;
		});
		$('#edit-warm-day-2').click(function(){
			open_links(2);
			return false;
		});
		$('#edit-warm-day-3').click(function(){
			open_links(3);
			return false;
		});
		$('#edit-warm-day-4').click(function(){
			open_links(4);
			return false;
		});
		$('#edit-warm-day-5').click(function(){
			open_links(5);
			return false;
		});
		$('#edit-warm-day-6').click(function(){
			open_links(6);
			return false;
		});
		$('#edit-warm-day-7').click(function(){
			open_links(7);
			return false;
		});

	});
})(jQuery);