console.log("hello world");

jQuery(document).ready(
	function(){

		jQuery(window)	
			.scroll(
				function(){
					if(trackingcookiesforjetpackpermission.hidescroll=="true"){
						jQuery("#trackingcookies").fadeOut();
					}
				}
			);

		jQuery("#trackingcookiesforjetpackpermissionaccept")
			.click(
				function(ev){		
			
					var data = {
						'action': 'trackingcookiesforjetpackpermission',
					};


					jQuery.post(trackingcookiesforjetpackpermission.ajaxurl, data, function(response) {
						console.log(response);
						jQuery("#trackingcookies").fadeOut();
						jQuery("body").append(response);
					});

				}
			);
		jQuery("#trackingcookiesforjetpackpermissionreject")
			.click(
				function(ev){
					jQuery(ev.currentTarget).parent().fadeOut(200);					
				}
			);

	}
);
