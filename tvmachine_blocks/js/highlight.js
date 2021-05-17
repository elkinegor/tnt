function do_highlight() {

 if(Drupal.tvmachine.highlight['highlight1']) {
	jQuery(".tvpeliculas").addClass("peliculas_highlight");
	jQuery(".cat_peliculas").addClass("cat_peliculas_highlight");
 }
 else {
	jQuery(".tvpeliculas").removeClass("peliculas_highlight");
	jQuery(".cat_peliculas").removeClass("cat_peliculas_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight2']) {
	jQuery(".tvseries").addClass("series_highlight");
	jQuery(".cat_series").addClass("cat_series_highlight");
 }
 else {
	jQuery(".tvseries").removeClass("series_highlight");
	jQuery(".cat_series").removeClass("cat_series_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight3']) {		
	jQuery(".tvdeportes").addClass("deportes_highlight");
	jQuery(".cat_deportes").addClass("cat_deportes_highlight");
 }
 else {
	jQuery(".tvdeportes").removeClass("deportes_highlight");
	jQuery(".cat_deportes").removeClass("cat_deportes_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight4']) {		
	jQuery(".tvnoticias").addClass("noticias_highlight");
	jQuery(".cat_noticias").addClass("cat_noticias_highlight");
 }
 else {
	jQuery(".tvnoticias").removeClass("noticias_highlight");
	jQuery(".cat_noticias").removeClass("cat_noticias_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight5']) {		
	jQuery(".tvinfantil").addClass("infantil_highlight");
	jQuery(".cat_infantil").addClass("cat_infantil_highlight");
 }
 else {
	jQuery(".tvinfantil").removeClass("infantil_highlight");
	jQuery(".cat_infantil").removeClass("cat_infantil_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight6']) {		
	jQuery(".tventretenimiento").addClass("entretenimiento_highlight");
	jQuery(".cat_entretenimiento").addClass("cat_entretenimiento_highlight");
 }
 else {
	jQuery(".tventretenimiento").removeClass("entretenimiento_highlight");
	jQuery(".cat_entretenimiento").removeClass("cat_entretenimiento_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight7']) {		
	jQuery(".tvdocumental").addClass("documental_highlight");
	jQuery(".cat_documental").addClass("cat_documental_highlight");
 }
 else {
	jQuery(".tvdocumental").removeClass("documental_highlight");
	jQuery(".cat_documental").removeClass("cat_documental_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight8']) {		
	jQuery(".tvcorazon").addClass("corazon_highlight");
	jQuery(".cat_corazon").addClass("cat_corazon_highlight");
 }
 else {
	jQuery(".tvcorazon").removeClass("corazon_highlight");
	jQuery(".cat_corazon").removeClass("cat_corazon_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight9']) {		
	jQuery(".tvconcursos").addClass("concursos_highlight");
	jQuery(".cat_concursos").addClass("cat_concursos_highlight");
 }
 else {
	jQuery(".tvconcursos").removeClass("concursos_highlight");
	jQuery(".cat_concursos").removeClass("cat_concursos_highlight");
 }
 
 if(Drupal.tvmachine.highlight['highlight10']) {		
	jQuery(".tvreality").addClass("reality_highlight");
	jQuery(".cat_reality").addClass("cat_reality_highlight");
 }
 else {
	jQuery(".tvreality").removeClass("reality_highlight");
	jQuery(".cat_reality").removeClass("cat_reality_highlight");
 }
}

(function ($, Drupal, settings) {
	$(document).ready(function () {
		
		$('.tvcat').hover(function(){
			$(this).css({cursor:'hand',cursor:'pointer',textdecoration:'underline'});
		},function(){
			$(this).css({textdecoration:'none'});
		});

		Drupal.tvmachine = {};
		Drupal.tvmachine.highlight = {
			highlight1:1,
			highlight2:1,
			highlight3:1,
			highlight4:0,
			highlight5:1,
			highlight6:1,
			highlight7:1,
			highlight8:0,
			highlight9:0,
			highlight10:0,
		};

		$('#peliculas').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight1'] == 0) {
				Drupal.tvmachine.highlight['highlight1'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight1'] = 0;
			}
			do_highlight();
		});
		
		$('#series').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight2'] == 0) {
				Drupal.tvmachine.highlight['highlight2'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight2'] = 0;
			}
			do_highlight();
		});
		
		$('#deportes').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight3'] == 0) {
				Drupal.tvmachine.highlight['highlight3'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight3'] = 0;
			}
			do_highlight();
		});
		
		$('#noticias').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight4'] == 0) {
				Drupal.tvmachine.highlight['highlight4'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight4'] = 0;
			}
			do_highlight();
		});	
		
		$('#infantil').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight5'] == 0) {
				Drupal.tvmachine.highlight['highlight5'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight5'] = 0;
			}
			do_highlight();
		});

		$('#entretenimiento').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight6'] == 0) {
				Drupal.tvmachine.highlight['highlight6'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight6'] = 0;
			}
			do_highlight();
		});
		
		$('#documental').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight7'] == 0) {
				Drupal.tvmachine.highlight['highlight7'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight7'] = 0;
			}
			do_highlight();
		});

		$('#corazon').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight8'] == 0) {
				Drupal.tvmachine.highlight['highlight8'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight8'] = 0;
			}
			do_highlight();
		});

		$('#concursos').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight9'] == 0) {
				Drupal.tvmachine.highlight['highlight9'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight9'] = 0;
			}
			do_highlight();
		});

		$('#reality').click(function(event) {
			if (Drupal.tvmachine.highlight['highlight10'] == 0) {
				Drupal.tvmachine.highlight['highlight10'] = 1;
			} else {
				Drupal.tvmachine.highlight['highlight10'] = 0;
			}
			do_highlight();
		});
	});
})(jQuery, Drupal);