Most of the code of this module resides at tvmachine_visibility module folder.
However, to make it work you've to:

1) Add this code:
// If we've function to hide unselected TV channels (i.e. tvmachine_visibility module is enabled)
if (typeof tvmachine_visibility_hide == 'function') {
  tvmachine_visibility_hide();
}
to tvmachine/js/mainframe.js file, line 150.

2) Add this code:
<!-- tvmachine Visibility scripts - to hide channels user has unchecked -->
<!-- We've to add scripts like this, since TVMachine module doesn't include standard Drupal script load methods -->
<link type="text/css" rel="stylesheet" media="all" href="/sites/default/modules/tvmachine_visibility/stylesheet.css" />
<script type="text/javascript" src="/misc/jquery.js"></script>
<script type="text/javascript" src="/sites/default/modules/tvmachine_visibility/script.js"></script>

to tvmachine/tvmachine_template_1_static.tpl.php file, line 78

3) Add code from sample-drupal-block-content.html to a certain page (probably as a Drupal block).
If it would be normal Drupal page, THAT'S ALL, if page is not that standard,
you'll need to attach script.js and style.css file manually, like we did at point 2.


Since tvmachine is a custom module, it doesn't have Drupal.org repository, and,
hence, will never ever receive semi-automatic update. So it's perfectly fine
to modify it, unlike other modules.

Note, that JavaScript-related settings are stored at script.js header.
