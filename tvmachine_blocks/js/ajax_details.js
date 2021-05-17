var SLIDEDOWN_SPEED = 100; // open speed, higher to increase slide speed
var SLIDEUP_SPEED = 200; //close speed
var xhr = null;
var m_iframe = null;


/**
* Slide down empty details box
*/
function newDetailsBox(link, chid, progid, msg) {

   window.programDiv = jQuery(link).parent();
   window.programDiv.css("borderBottomColor", programDiv.css("backgroundColor"));

   var c = jQuery("#ajax-here");

	var html = "<div id=\"program-details-ajax-" + progid + "\""
      + " class=\"program-details-ajax home-details-ajax loading_2\""
      + " style='display:block;overflow:hidden;'>"
	  + "<iframe class='iframe-details' width='100%' scrolling='no' frameborder='0' src='' style='height: 328px;'></iframe>"
    + "</div>"; 	  
   var detailsWrapper = jQuery(html);	
   detailsWrapper.css("backgroundColor", programDiv.css("backgroundColor"));   
   
   // Insert the iframe after the row of the cannel
   jQuery("#" + chid).after(detailsWrapper);
   
   detailsWrapper.children("iframe").on('load', function(){
		m_iframe = this;
		setTimeout(function() {
			if (m_iframe) {
				var height = m_iframe.contentWindow.document.getElementById('detail-program-content').offsetHeight;
				m_iframe.style.height = height + 'px';
				var detailsWrapper = jQuery(m_iframe).parent();
				detailsWrapper.stop().removeClass("loading_2");
				// slide down the details box
				var diff = height - detailsWrapper.height();
				if (diff > 0) {
					detailsWrapper.animate({height: height}, diff * (100 / SLIDEDOWN_SPEED), "linear");
				}
				m_iframe = null;
			}
			
		}, 0);
	}).attr('src', link.href);		// set the src after setting up everything else.			

	detailsWrapper.animate({height: "328px"}, 200 * (100 / SLIDEDOWN_SPEED), "linear");
   
	return detailsWrapper;
}


/*
* Helper function to set program box bottom border color to black
*/
function restoreBorder() {
   if(window.programDiv) {
      window.programDiv.css("borderBottomColor", window.programDiv.css("borderRightColor"));
      window.programDiv = null;
   }
}

/**
* Closes previous and opens new details box for the program
*/
function ondetails(link, chid, progid) {

   var detailsDiv =  jQuery(".program-details-ajax"); // find already existing details box
   
   if(jQuery("#program-details-ajax-" + progid).length) { 
      // user clicks already opened detailed box, simply close it
      closeDetailsBox(detailsDiv, function() {});
   }
   else if(detailsDiv.length) {  
      // another program details box is opened, close previous
      var msg1;
      var detailsWrapper = null;
      
      closeDetailsBox(detailsDiv, function() {
         detailsWrapper = newDetailsBox(link, chid, progid, msg1);
      });      
   }
   else {
      // no other detail DIVs are opened, simply open new details box
      var detailsWrapper = newDetailsBox(link, chid, progid);      
   }

   return false;
};

/**
* Helper function to close program details box
*/
function closeDetailsBox(detailsDiv, onSuccessCallback) {
   var container = jQuery("#ajax-here"); //container holds our programs inside channels
   
   // calculate top offset of the details box inside the container
   var offsetTop = 0;
   jQuery.each(detailsDiv.prevAll(), function(index, child){
      offsetTop += parseInt(jQuery(child).outerHeight(true));
   });

    
   // get the visible height of the details box
   var visibleHeight = detailsDiv.height() - (container.scrollTop() - offsetTop);
   if(visibleHeight < 0) { 
      visibleHeight = 0; 
   }
   
   if(container.scrollTop() > offsetTop && visibleHeight > 0) {
      // if details div is allready opened, and is partly visible then
      // slide up the visible part, and remove the rest changing the scroll
      
      var invisibleHeight = detailsDiv.height() - visibleHeight;
      var bottomSpace = container[0].scrollHeight - container.scrollTop() - container.innerHeight(); 
       
      //alert('bottomSpace=' + bottomSpace + ', invisibleHeight=' + invisibleHeight + ', visibleHeight=' + visibleHeight);
      
      if(bottomSpace < visibleHeight) {
         var slideToHeight = 0;
      }
      else {
         var slideToHeight = invisibleHeight;
      }
      var slideHeight = visibleHeight;
   }
   else if((container.scrollTop() > offsetTop && visibleHeight == 0) 
   || (container.scrollTop() + container.innerHeight() < offsetTop)) {
      // if there is an opened details div, and its not visible, then remove it
      var slideToHeight = 0;
      var slideHeight = 0;
   }
   else {
      // if there is an opened details div, its partly visible and its in the bottom, then slide only visible height
      var height = container.scrollTop() + container.innerHeight() - offsetTop;
      if(height > detailsDiv.height())  {
          height = detailsDiv.height();
      }
      detailsDiv.height(height);
      
      var slideToHeight = 0;
      var slideHeight = detailsDiv.height();
   }

   detailsDiv.animate({height: slideToHeight}, (slideHeight * (100/SLIDEUP_SPEED)) + 1, "linear", function() {
      if(slideToHeight > 0) {
         detailsDiv.height(0);
         container.scrollTop(offsetTop);
      }
      detailsDiv.remove();
      restoreBorder();
      onSuccessCallback();
   });            
}


// called when document load event
// trung 15 sept 2011
//
//Drupal.behaviors.tv_details = function() {
Drupal.behaviors.tv_details = {
   attach: function (context, settings) {

      jQuery('a.a-details').unbind('click').click(function(){
   		var a = jQuery(this);
   		var chid = 'channel-' + a.attr('ch');
   		var progid = a.attr('p');
   		ondetails(this, chid, progid);
   		return false;						// disable a click event
      });
   }
};
