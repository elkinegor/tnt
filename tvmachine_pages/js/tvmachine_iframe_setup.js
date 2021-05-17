(function ($) {

	$(document).ready(function () {

		function executeAction() {
			var srcMainFrame = window.tvmachine['base_url']+'/program/content/'+window.tvmachine['template']+'/'+'1'+'/'+'0'+'/'+'0'+'/'+window.tvmachine['hour']+'/'+window.tvmachine['minute']+'/'+window.tvmachine['color1']+'/'+window.tvmachine['color2']+'/'+window.tvmachine['color3']+'/'+window.tvmachine['color4'];
			var srcMainFrame_textarea = srcMainFrame;
			var textareaContent = '<iframe name=\"mainframe\" id=\"mainframe\" src=\"'+srcMainFrame_textarea+'\" scrolling=\"no\" width=\"'+window.tvmachine['mainframe_width']+'\" height=\"'+window.tvmachine['mainframe_height']+'\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\"></iframe><noscript><a href="https://www.tvguia.es">Programacion TV</a></noscript>';
			$('#textarea').val(textareaContent);
			$('#mainframe')[0].src = srcMainFrame;
		}		

		$('#select-hour').change(function(){
			window.tvmachine['hour'] = parseInt($(this)[0].value);
			executeAction();
		});

		$('#select-minute').change(function(){
			window.tvmachine['minute'] = parseInt($(this)[0].value);
			executeAction();
		});

		$('#preview-colors').click(function(){
			var color1 = $('#input_field_1').val() || "#" + window.tvmachine['color1'];
			window.tvmachine['color1'] = color1.replace(/#/,'');
			var color2 = $('#input_field_2').val() || "#" + window.tvmachine['color2'];
			window.tvmachine['color2'] = color2.replace(/#/,'');
			var color3 = $('#input_field_3').val() || "#" + window.tvmachine['color3']; // timebar color
			window.tvmachine['color3'] = color3.replace(/#/,'');
			var color4 = $('#input_field_4').val() || "#" + window.tvmachine['color4'];
			window.tvmachine['color4'] = color4.replace(/#/,'');
			executeAction();
		});

	});
})(jQuery);