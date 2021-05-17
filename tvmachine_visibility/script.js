// SETTINGS

// Set path to selectbox images
var selectbox_checked_img_url = "/modules/custom/tvmachine_visibility/selectbox-checked.png";
var selectbox_unchecked_img_url = "/modules/custom/tvmachine_visibility/selectbox-unchecked.png";

// Set URLs, where we don't want to hide any channels, no matter what.
// You can list multiple pages, like this:
// var URLsDontHideChannelsList = ["/page/sondeo-noche-tv", "/mobile/sondeo-noche-tv"];
var URLsDontHideChannelsList = ["/page/sondeo-noche-tv","/mobile/sondeo-noche-tv","/mobile/sondeo-noche"];

// Tasks to do right after page loading process had been finished
jQuery(document).ready(function() {
  // Hide certain channels based on cookie values
  tvmachine_visibility_hide();
  // Uncheck certain channel groups based on cookie values
  tvmachine_visibility_uncheck_channel_group_selects();
  // Uncheck certain channels based on cookie values
  tvmachine_visibility_uncheck_channels_selects();
});



// Hide certain channels based on cookie values
function tvmachine_visibility_hide() {
  // Check if our script is not blocked on this page
  // Iterate through all "blocked" URLs, one by one
  for (var i = 0; i < URLsDontHideChannelsList.length; i++) {
    if (window.location.pathname == URLsDontHideChannelsList[i]) {
      // Current page is in restricted list, terminate function execution.
      return "We should not hide any channels at this page.";
    }
  }

  // Get respective cookie value
  var channels_to_hide_json = getCookie("tv_channels_to_hide");

  // If cookie is not empty
  if (channels_to_hide_json) {
    // Parse JSON to JavaScript array
    var channels_to_hide = JSON.parse(channels_to_hide_json);

    // Hide all channels mentioned in a respective cookie, one by one
    for (var i = 0; i < channels_to_hide.length; i++) {
      // Construct HTML element channel ID
      var channel_to_hide = "#" + channels_to_hide[i];
      // Hide channel
      jQuery(channel_to_hide).addClass("visibility_hide_element");
    }
  }
}



// Uncheck certain channels (at channel selection pages) based on cookie values
function tvmachine_visibility_uncheck_channels_selects() {
  // We should re-write "selectbox" image URLs, since they're being modified by lazy_image_loader Drupal module
  // We can't do it on page load
  jQuery(".selectbox-checked").attr("src",selectbox_checked_img_url);
  jQuery(".selectbox-unchecked").attr("src",selectbox_unchecked_img_url);
  
  // Get cookie with unchecked channels list
  var channels_unchecked_json = getCookie("tv_channels_to_hide");
  var channels_unchecked_arr = [];
  
  // If cookie is not empty
  if(channels_unchecked_json) {
    // Parse JSON to JavaScript array
    channels_unchecked_arr = JSON.parse(channels_unchecked_json);

    // Uncheck all channels mentioned in a respective cookie, one by one
    for (var i = 0; i < channels_unchecked_arr.length; i++) {
      // Construct HTML element channel CSS class
      // CSS class for particular channel checkbox should looks like this: .visibility-channel-61
      var checkboxClass = ".visibility-" + channels_unchecked_arr[i];
      // Hide checked selectbox image
      jQuery(checkboxClass + ".selectbox-checked").addClass("visibility_hide_element");
      // Show unchecked selectbox image
      jQuery(checkboxClass + ".selectbox-unchecked").removeClass("visibility_hide_element");
    }
  }
}



// Uncheck certain channel groups (at channel selection pages) based on cookie values
function tvmachine_visibility_uncheck_channel_group_selects() {
  // We should re-write "selectbox" image URLs, since they're being modified by lazy_image_loader Drupal module
  // We can't do it on page load
  jQuery(".selectbox-checked").attr("src",selectbox_checked_img_url);
  jQuery(".selectbox-unchecked").attr("src",selectbox_unchecked_img_url);
  
  // Get cookie with unchecked channel groups list
  var channel_groups_unchecked_json = getCookie("tv_channels_group_unchecked");
  var channel_groups_unchecked_arr = [];
  
  // If cookie is not empty
  if(channel_groups_unchecked_json) {
    // Parse JSON to JavaScript array
    channel_groups_unchecked_arr = JSON.parse(channel_groups_unchecked_json);

    // Uncheck all channel groups mentioned in a respective cookie, one by one
    for (var i = 0; i < channel_groups_unchecked_arr.length; i++) {
      // Construct HTML element channel group CSS class, like channels-group-main-header-checkbox
      var checkboxClass = "." + channel_groups_unchecked_arr[i] + "-header-checkbox";
      // Hide checked selectbox image
      jQuery(checkboxClass + ".selectbox-checked").addClass("visibility_hide_element");
      // Show unchecked selectbox image
      jQuery(checkboxClass + ".selectbox-unchecked").removeClass("visibility_hide_element");
    }
  }
}

// "Selectbox image" associated with a particular group of channels was clicked
function tvmachine_visibility_channels_clicked(channelsCSSclass) {
  // We should re-write "selectbox" image URLs, since they're being modified by lazy_image_loader Drupal module
  // We can't do it on page load
  jQuery(".selectbox-checked").attr("src",selectbox_checked_img_url);
  jQuery(".selectbox-unchecked").attr("src",selectbox_unchecked_img_url);
  
  // Checkbox class for this channel group, like .channels-group-main-checkbox
  var indChannelCheckboxClass = "." + channelsCSSclass + "-checkbox";
  // Individual channel class for this channel group, like .channels-group-main-channel
  var indChannelClass = "." + channelsCSSclass + "-channel";
  
  // Find out, if checkbox is checked (based on cookie value)
  var channel_group_cookie = getGroupCookieValue(channelsCSSclass);
  var channel_group_checked = channel_group_cookie["checked"];
  var channel_group_checked_new = "";
  
  if (channel_group_checked == "checked" || channel_group_checked == "empty_cookie") {
    // Channel group is checked now, make it unchecked
    // By default, all groups and channels are checked, thus if cookie is not set yet, we consider group as checked
    // New group status, opposite to channel_group_checked value
    channel_group_checked_new = "unchecked";
    
    // Calculate and initiate save process of a new cookie value
    newGroupCookieValue(channelsCSSclass, channel_group_checked_new, channel_group_cookie["arr"]);

    // Hide checked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-checked").addClass("visibility_hide_element");
    // Show unchecked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-unchecked").removeClass("visibility_hide_element");
  } else if (channel_group_checked == "unchecked") {
    // If channel group is unchecked now, make it checked
    // New group status, opposite to channel_group_checked value
    channel_group_checked_new = "checked";

    // Calculate and initiate setting of a new cookie value
    newGroupCookieValue(channelsCSSclass, channel_group_checked_new, channel_group_cookie["arr"]);

    // Show checked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-checked").removeClass("visibility_hide_element");
    // Hide unchecked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-unchecked").addClass("visibility_hide_element");
  }



  // Iterate through all channels in this group to check or uncheck each of them
  jQuery(indChannelClass).each(function () {
    // We should re-write "selectbox" image URLs, since they're being modified by lazy_image_loader Drupal module
    // We can't do it on page load
    jQuery(".selectbox-checked").attr("src", selectbox_checked_img_url);
    jQuery(".selectbox-unchecked").attr("src", selectbox_unchecked_img_url);
    
    // Get particular channel ID, like channel-61
    var channelID = jQuery(this).attr('data-channel-id');
    // CSS class for particular channel checkbox should looks like this: visibility-channel-61
    var indChannelCheckboxClass = ".visibility-" + channelID;

    // Retrieve channel-related cookie info, just to get cookie values array
    var channel_cookie = getChannelCookieValue(channelID);

    if (channel_group_checked_new == "unchecked") {
      // Channel should be unchecked
      // Calculate and initiate save process of a new cookie value
      newChannelCookieValue(channelID, "unchecked", channel_cookie["arr"]);

      // Hide checked selectbox image
      jQuery(indChannelCheckboxClass + ".selectbox-checked").addClass("visibility_hide_element");
      // Show unchecked selectbox image
      jQuery(indChannelCheckboxClass + ".selectbox-unchecked").removeClass("visibility_hide_element");
    } else if (channel_group_checked_new == "checked") {
      // Channel should be checked
      // Calculate and initiate save process of a new cookie value
      newChannelCookieValue(channelID, "checked", channel_cookie["arr"]);

      // Show checked selectbox image
      jQuery(indChannelCheckboxClass + ".selectbox-checked").removeClass("visibility_hide_element");
      // Hide unchecked selectbox image
      jQuery(indChannelCheckboxClass + ".selectbox-unchecked").addClass("visibility_hide_element");
    }
  });
}



// Individual channel-associated "selectbox image" was clicked
function tvmachine_visibility_channel_clicked(channelDIVelement) {
  // We should re-write "selectbox" image URLs, since they're being modified by lazy_image_loader Drupal module
  // We can't do it on page load
  jQuery(".selectbox-checked").attr("src",selectbox_checked_img_url);
  jQuery(".selectbox-unchecked").attr("src",selectbox_unchecked_img_url);
  
  // Get particular channel ID, like channel-61
  var channelID = jQuery(channelDIVelement).attr('data-channel-id');
  // CSS class for particular channel checkbox should looks like this: visibility-channel-61
  var indChannelCheckboxClass = ".visibility-" + channelID;
  
  // Find out, if checkbox is checked (based on cookie value)
  var channel_cookie = getChannelCookieValue(channelID);
  var channel_checked = channel_cookie["checked"];

  if (channel_checked == "checked" || channel_checked == "empty_cookie") {
    // Channel is checked now, make it unchecked
    // By default, all groups and channels are checked, thus if cookie is not set yet, we consider channel as checked
    
    // Calculate and initiate save process of a new cookie value
    newChannelCookieValue(channelID, "unchecked", channel_cookie["arr"]);

    // Hide checked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-checked").addClass("visibility_hide_element");
    // Show unchecked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-unchecked").removeClass("visibility_hide_element");
  } else if (channel_checked == "unchecked") {
    // If channel group is unchecked now, make it checked

    // Calculate and initiate save process of a new cookie value
    newChannelCookieValue(channelID, "checked", channel_cookie["arr"]);

    // Show checked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-checked").removeClass("visibility_hide_element");
    // Hide unchecked selectbox image
    jQuery(indChannelCheckboxClass + ".selectbox-unchecked").addClass("visibility_hide_element");
  }
}



// Calculate cookie value with individual channels IDs
function calcCookieValue(channelID) {
  // Get respective cookie value
  var channels_to_hide_json = getCookie("tv_channels_to_hide");
  var channels_to_hide_arr = [];
  
  // If cookie is not empty
  if(channels_to_hide_json) {
    // Parse JSON to JavaScript array
    channels_to_hide_arr = JSON.parse(channels_to_hide_json);
    
    // Find Channel ID position in array, if any
    var ifChannelInArr = jQuery.inArray(channelID, channels_to_hide_arr);

    if(ifChannelInArr != -1) {
      // Remove channel from array
      channels_to_hide_arr.splice(ifChannelInArr, 1);

    } else {
      // Append new Channel ID to the array
      channels_to_hide_arr.push(channelID);
    }

  } else {
    // Cookie is empty
    channels_to_hide_arr.push(channelID);
  }
  
  // Convert array to JSON text string
  var channels_to_hide_json = JSON.stringify(channels_to_hide_arr);
  // Initiate new cookie value setting process
  setCookie("tv_channels_to_hide", channels_to_hide_json, 900);
}



// Get current cookie value for particular channel CSS class
function getChannelCookieValue(channelID) {
  // Get cookie with unchecked channels list
  var channels_unchecked_json = getCookie("tv_channels_to_hide");
  var channels_unchecked_arr = [];
  var channel_cookie = {};
  
  
  // If cookie is not empty
  if(channels_unchecked_json) {
    // Parse JSON to JavaScript array
    channels_unchecked_arr = JSON.parse(channels_unchecked_json);

    // Check if channel CSS class is in array
    var ifChannelInArr = jQuery.inArray(channelID, channels_unchecked_arr);

    if(ifChannelInArr != -1) {
      // Channel is unchecked
      channel_cookie["checked"] = "unchecked";
    } else {
      // Channel is checked
      channel_cookie["checked"] = "checked";
    }

  } else {
    // Cookie is empty
    channel_cookie["checked"] = "empty_cookie";  
  }
  // Save array with all values for respective cookie at variable
  channel_cookie["arr"] = channels_unchecked_arr;
  return channel_cookie;
}



// Calculate new cookie value with channel IDs
// channelIfChecked argument - new value to set
// channels_unchecked_arr argument - array with all values for respective cookie
function newChannelCookieValue(channelID, channelIfChecked, channels_unchecked_arr) {
  // Find Channel ID position in array, if any
  var ifChannelInArr = jQuery.inArray(channelID, channels_unchecked_arr);

  if (channelIfChecked == "checked") {
    // Channel should be marked as checked, i.e. removed from array, if in

    if (ifChannelInArr != -1) {
      // Channel was in array, now we'll mark it as checked
      // Remove channel from array
      channels_unchecked_arr.splice(ifChannelInArr, 1);
    }
    // If channel was not in array, we don't have to do anything about it, leave everything as is

  } else if (channelIfChecked == "unchecked") {
    // Channel should be marked as unchecked, i.e. added to array
    if (ifChannelInArr == -1) {
      // Channel ID was not in array
      // Append new Channel ID to the array
      channels_unchecked_arr.push(channelID);
    }
    // If channel was in array, we don't have to do anything about it, leave everything as is

  }

  // Convert array to JSON text string
  var channels_unchecked_json = JSON.stringify(channels_unchecked_arr);
  // Initiate new cookie value setting process
  setCookie("tv_channels_to_hide", channels_unchecked_json, 900);
}



// Get current cookie value for particular channel group CSS class
function getGroupCookieValue(channelGroupID) {
  // Get cookie with unchecked channel groups list
  var channel_groups_unchecked_json = getCookie("tv_channels_group_unchecked");
  var channel_groups_unchecked_arr = [];
  var channel_group_cookie = {};
  
  
  // If cookie is not empty
  if(channel_groups_unchecked_json) {
    // Parse JSON to JavaScript array
    channel_groups_unchecked_arr = JSON.parse(channel_groups_unchecked_json);

    // Check if group CSS class is in array
    var ifChannelGroupInArr = jQuery.inArray(channelGroupID, channel_groups_unchecked_arr);

    if(ifChannelGroupInArr != -1) {
      // Group is unchecked
      channel_group_cookie["checked"] = "unchecked";
    } else {
      // Group is checked
      channel_group_cookie["checked"] = "checked";
    }

  } else {
    // Cookie is empty
    channel_group_cookie["checked"] = "empty_cookie";  
  }
  
  // Save array with all values for respective cookie at variable
  channel_group_cookie["arr"] = channel_groups_unchecked_arr;
  return channel_group_cookie;
}



// Calculate new cookie value with channel groups IDs
// channelGroupIfChecked argument - new value to set
// channel_groups_unchecked_arr argument - array with all values for respective cookie
function newGroupCookieValue(channelGroupID, channelGroupIfChecked, channel_groups_unchecked_arr) {
  // Find Channel Group ID position in array, if any
  var ifChannelGroupInArr = jQuery.inArray(channelGroupID, channel_groups_unchecked_arr);

  if (channelGroupIfChecked == "checked") {
    // Channel group should be marked as checked, i.e. removed from array, if in

    if (ifChannelGroupInArr != -1) {
      // Group was in array, now we'll mark it as checked
      // Remove channel group from array
      channel_groups_unchecked_arr.splice(ifChannelGroupInArr, 1);
    }
    // If group was not in array, we don't have to do anything about it, leave everything as is

  } else if (channelGroupIfChecked == "unchecked") {
    // Channel group should be marked as unchecked, i.e. added to array
    if (ifChannelGroupInArr == -1) {
      // Channel Group ID was not in array
      // Append new Channel Group ID to the array
      channel_groups_unchecked_arr.push(channelGroupID);
    }
    // If group was in array, we don't have to do anything about it, leave everything as is

  }

  // Convert array to JSON text string
  var channel_groups_unchecked_json = JSON.stringify(channel_groups_unchecked_arr);
  // Initiate nev cookie value setting process
  setCookie("tv_channels_group_unchecked", channel_groups_unchecked_json, 900);
}



// Low-level function to set cookie
// Provide cookie name, value and validity period from now, in days
// If validity period is not set, cookie will be deleted right after session has closed
function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 2000));
  var expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}



// Low-level function to get particular cookie value
// Provide particular cookie name as an argument
function getCookie(cname) {
  var name = cname + "=";
  // Decode ALL the cookies
  var decodedCookie = decodeURIComponent(document.cookie);
  // Split them to individual ones
  var ca = decodedCookie.split(';');
  // Iterate through all individual cookies
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}