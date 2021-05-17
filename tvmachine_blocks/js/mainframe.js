function setButtonDayActive(dayURL,dayCur,dayTom,dayAft,dayYest,dayBeY){

	var which = '';

	switch (dayURL)
	{
		case dayCur:
			which = document.getElementById('button-today');
		break;
		case dayTom:
			which = document.getElementById('button-tomorrow');
		break;
		case dayAft:
			which = document.getElementById('button-after-tomorrow');
		break;
		case dayYest:
			which = document.getElementById('button-yesterday');
		break;
		case dayBeY:
			which = document.getElementById('button-before-yesterday');
		break;
	}

	if (which) {
        button_active(which);
    }
}

function changeHour(template,hourStart)
{
    hourStart = parseInt(hourStart);
    template  = parseInt(template);
    for(var i=0;i<24;i++)
    {
        if(i==hourStart)
        {
            if(template==1 || template==5 || template==9)
            {
                if(hourStart%2==0) {
                    var hourEnd = hourStart + 2;
                } else {
                    hourStart = hourStart-(hourStart%2);
                    var hourEnd =   hourStart+2;
                }
            }
            if(template==2 || template==6)
            {
                 if(hourStart%3==0) {
                    var hourEnd = hourStart+3;
                 } else {
                    hourStart = hourStart-(hourStart%3);
                    var hourEnd =   hourStart+3;
                 }
            }
            var id1 = (hourStart<=9)?'0'+hourStart:hourStart;
            var id2 = (hourEnd<=9)?'0'+hourEnd:hourEnd;
            var buttonId = 'button-'+id1+'-'+id2;

            var buttonEle = document.getElementById(buttonId);
            button_active(buttonEle);
            changeTimeBar(Number(template),Number(hourStart));
            break;
        }
    }
}

function callback(temp, day1_n, day1_j, this_var) {
   if (arguments.length > 3) { // this case is day button clicked
	   button_active(arguments[3]);
	   setValue(arguments[1],arguments[2]);
   } // this case is hour button clicked
   else {
	   button_active(arguments[2]);
	   setValue(arguments[1]);
	   changeTimeBar(arguments[0],arguments[1])
   }
   showTVProgram(arguments[0]);
}

function callback_set(template, set, element, base_url) {
   button_active(element);
   
   showTVProgram(template);
}

function setValue() {
	if (arguments.length > 1) { // this case is day button clicked
		window.tvmachine['month'] = arguments[0];
		window.tvmachine['day'] = arguments[1];
	}
	else {
		window.tvmachine['hour'] = arguments[0];
	}
}

function button_active(ele) {
    // className attribute is usefull for IE, Firefox, GChrome , Safari, Opera

    if (ele) {

        var class_name = ele.className;
        var classArray = class_name.split(" ");

        class_name = classArray[1].replace("MSI_ext_nofollow","");
        class_name = class_name.replace(" ","");

        switch (class_name) 
        {
            case 'button-yesterday' : 
                jQuery('.button-tomorrow-active').removeClass('button-tomorrow-active').addClass('button-tomorrow');
                jQuery('.button-today-active').removeClass('button-today-active').addClass('button-today');
                jQuery('.button-yesterday-active').removeClass('button-yesterday-active').addClass('button-yesterday');
                ele.className = 'tv-button button-yesterday-active';
                break;

            case 'button-today' : 
                jQuery('.button-tomorrow-active').removeClass('button-tomorrow-active').addClass('button-tomorrow');
                jQuery('.button-yesterday-active').removeClass('button-yesterday-active').addClass('button-yesterday');
                ele.className = 'tv-button button-today-active';
                break;

            case 'button-tomorrow': 
                jQuery('.button-today-active').removeClass('button-today-active').addClass('button-today');
                jQuery('.button-tomorrow-active').removeClass('button-tomorrow-active').addClass('button-tomorrow');
                jQuery('.button-arrow-active').removeClass('button-arrow-active').addClass('button-arrow');
                jQuery('.button-yesterday-active').removeClass('button-yesterday-active').addClass('button-yesterday');
                ele.className = 'tv-button button-tomorrow-active';
                break;

            case 'button-time': 
                jQuery('.button-time-active').removeClass('button-time-active').addClass('button-time');
                jQuery('.button-arrow-active').removeClass('button-arrow-active').addClass('button-arrow');
                ele.className = 'tv-button button-time-active';
                break;

            case 'button-arrow': 
                jQuery('.button-arrow-active').removeClass('button-arrow-active').addClass('button-arrow');
                ele.className = 'tv-button button-arrow-active';
                break;
        }
    }
}

function showTVProgram(template) {

	var url_ajax = window.tvmachine['base_url'] + '/program/ajax/'+window.tvmachine['template']+'/1/'+window.tvmachine['month']+'/'+window.tvmachine['day']+'/'+window.tvmachine['hour']+'/'+window.tvmachine['minute']+'/'+window.tvmachine['color1']+'/'+window.tvmachine['color2']+'/'+window.tvmachine['color3']+'/'+window.tvmachine['color4'];

	jQuery("#ajax-here").html('<div style="font-family:verdana;color:#'+window.tvmachine['color4']+';font-weight:bold; text-align:center; font-size:12px;">Cargando Parrilla...</div>').addClass('loading');

jQuery.ajax({
		type: 'GET',
		url: url_ajax,
		success: function(msg){

	    var ajax = jQuery("#ajax-here").removeClass('loading').html(msg);

		Drupal.attachBehaviors(jQuery("#ajax-here").get(0));

      //restore category highlights also
      if (typeof do_highlight == 'function') {
      	do_highlight();
      }

      // If we've function to hide unselected TV channels
      if (typeof tvmachine_visibility_hide == 'function') {
        tvmachine_visibility_hide();
      }

		}
	});
}

function changeTimeBar(template,hourStart) {
    var hour_start = hourStart;
    var minute_start;
    if(template==1 || template==5 || template==9) {
        jQuery('.tvtime-hour').each(function(i){
            if ((i==2)||(i==3)) hour_start = hourStart+1;
            if (i==4) hour_start = hourStart+2;

            if ((i==1)||(i==3)) {
                minute_start = '30';
            }
			else if (i==4){
                minute_start = '';
            } else {
                minute_start = '00';
            }
            var text = '' + hour_start + ':' + minute_start;
            jQuery(this).text(text);
        });
    }
    if(template==2 || template==6) {
        jQuery('.tvtime-hour').each(function(i){
            if ((i==2)||(i==3)) hour_start = hourStart+1;
            if ((i==4)||(i==5)) hour_start = hourStart+2;
            if (i==6) hour_start = hourStart+3;

            if ((i==1)||(i==3)||(i==5)){
                minute_start = '30';
            } else {
                minute_start = '00';
            }
            var text = '' + hour_start + ':' + minute_start;
            jQuery(this).text(text);
        });
    }
}

function getCookie(c_name)
{
   if (document.cookie.length>0)
     {
     c_start=document.cookie.indexOf(c_name + "=");
     if (c_start!=-1)
       {
       c_start=c_start + c_name.length+1;
       c_end=document.cookie.indexOf(";",c_start);
       if (c_end==-1) c_end=document.cookie.length;
       return unescape(document.cookie.substring(c_start,c_end));
       }
     }
   return "";
}

function setCookie(c_name,value,expiredays)
{
   var exdate=new Date();
   exdate.setDate(exdate.getDate()+expiredays);
   document.cookie=c_name+ "=" +escape(value)+
   ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}