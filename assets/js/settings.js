jQuery(document).ready(function() {
	var widget_location = function() {
		if ( jQuery('#yotpo-location').length && jQuery('#yotpo-location').val() == 'tab' ) {
			jQuery('#tab_name').show();
			jQuery('#jsinject_selector').hide();
			jQuery("#tab_custom_location").collapse('hide');
		} else if ( jQuery('#yotpo-location').length && jQuery('#yotpo-location').val() == 'other' ) {
			jQuery('#tab_name').hide();
			jQuery('#jsinject_selector').hide();
			jQuery("#tab_custom_location").collapse('show');
		} else if ( jQuery('#yotpo-location').length && jQuery('#yotpo-location').val() == 'jsinject' ) {
			jQuery('#jsinject_selector').show();
			jQuery('#tab_name').hide();
			jQuery("#tab_custom_location").collapse('hide');
		} else {
			jQuery("#tab_custom_location").collapse('hide');
			jQuery('#tab_name').hide();
			jQuery('#jsinject_selector').hide();
		}
	}
	jQuery('#yotpo-location').change(function() { widget_location(); })
	widget_location();
	var debug_level = function() {
		if ( jQuery('#debug-enabled').length && jQuery('#debug-enabled')[0].checked ) {
			jQuery('#debug_level').show();
		} else {
			jQuery('#debug_level').hide();
		}
	}
	debug_level();
	jQuery('#debug-enabled').change(function() { debug_level(); })
	jQuery('[data-toggle="tooltip"]').tooltip({container: '#bootstrap-wrapper', boundary: 'window'});
})