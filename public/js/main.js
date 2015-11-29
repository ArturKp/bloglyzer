(function(){
	'use strict';

	jQuery('a[data-expand]').on('click', function () {
		var selector = jQuery(this).data('expand');
		jQuery(selector).show();
		jQuery(this).hide();
	});

})();