(function($) {
	$(".toggle-l").click(function () {
		$('body').toggleClass("slide-theme-nav");
	});



	/*$(".toggle-r").click(function () {
		$('body').toggleClass("slide-theme-helper");
	});*/

	// $( ".kmt-ratings-stars .ui-stars-cancel a" ).append( "<span>Clear ratings</span>" );
	// $( ".kmt-ratings-stars .ui-stars-cancel a" ).addClass( "Hero" );

	// $("#theme-subnav a.pull-right").parent('li').addClass('pull-right');

	// if ($(".theme-nav .nav > li.parent.active").length > 0) {
	// 	$(".theme-nav").addClass("display-child");
	// }

})(jQuery);


EasySocial.ready(function($){


	$(window).on("resize", 
	$.debounce(function(event, responsive) {

		if ($(window).width() < 780) {

			 $("[data-es-provide=tooltip]")
				.attr("data-es-provide", "disabled-tooltip")
				.tooltip("destroy");

		} else {

			$("[data-es-provide=disabled-tooltip]")
				.attr("data-es-provide", "tooltip");
		}
	}, 250));


	// Add active class on notifications module's button when popbox shows up
	$(document)
		.on("popboxActivate", ".mod-es-notifications", function(event, popbox){
			popbox.button.addClass("active");
		})
		.on("popboxDeactivate", ".mod-es-notifications", function(event, popbox){
			popbox.button.removeClass("active");
		});

});

