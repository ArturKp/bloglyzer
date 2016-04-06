(function(){
	'use strict';

	jQuery('a[data-expand]').on('click', function () {
		var selector = jQuery(this).data('expand');
		jQuery(selector).show();
		jQuery(this).hide();
	});

	jQuery('#superwords-cb').change(function() {
		if(this.checked) {
			jQuery('.word-usage-list li:not(.superword)').hide();
		}
		else
		{
			jQuery('.word-usage-list li').show();
		}
	});

})();