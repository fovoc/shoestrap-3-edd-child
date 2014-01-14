$(function(){
	//ISOTOPE
	var $container = $(".product-list");
	var $default_name_label 	= $(".btn-name").text();
	var $default_price_label 	= $(".btn-price").text();

	$(".filter select").multiselect({
   	enableCaseInsensitiveFiltering: true,
   	dropRight: true,
   	nonSelectedText: "No filters",
  });
	
	var $checkboxes = $(".multiselect-container li a");

	if ( shoestrap_script_vars.equalheights == 1 )
   	$(".product-list .type-download").equalHeights();

  $container.isotope({
	  layoutMode: "sloppyMasonry",
	  itemSelector: ".type-download",
	  animationEngine: "best-available",
	  // get sort data-filter
	  getSortData : {
	    name : function ( $elem ) {
	      return $elem.find(".name").text();
	    },
	    price : function ( $elem ) {
	      return parseInt( $elem.find(".price").text(), 10 );
	    }
	  }
	});

  $checkboxes.click(function(){
    var filters = [];
   	var active = $(".filter select").val();
    if ( active ) 
    	filters.push(active);
		filters = filters.join(", ");
    $container.isotope({ filter: filters });
    if ( $(".filter .multiselect").text() != "No filters " ) 
			$(".filter .multiselect").removeClass("btn-default").addClass("btn-primary");
		else 
			$(".filter .multiselect").removeClass("btn-primary").addClass("btn-default");	
  });

  $(".sort .true a").click(function(){
	  // get href attribute, minus the "#"
	  var sortName = $(this).attr("href").slice(1);
	  var order = $(this).text();
	  if ( sortName == "name" ) {
	  	$(".btn-name .name").html($default_name_label).append(" ").append(order);
	  	$(".btn-price .name").html( $default_price_label );
	  	$(".btn-name").addClass("btn-primary");
	  	$(".btn-price").removeClass("btn-primary");
	  }
	  if ( sortName == "price" ) {
	  	$(".btn-price .name").html( $default_price_label ).append(" ").append(order);
	  	$(".btn-name .name").html( $default_name_label );
	  	$(".btn-price").addClass("btn-primary");
	  	$(".btn-name").removeClass("btn-primary");
	  }
	  $container.isotope({ sortBy : sortName, sortAscending : true });
	  return false;
	});

  $(".sort .false a").click(function(){
	  // get href attribute, minus the "#"
	  var sortName = $(this).attr("href").slice(1);
	  var order = $(this).text();
	  if ( sortName == "name" ) {
	  	$(".btn-name .name").html( $default_name_label ).append(" ").append(order);
	  	$(".btn-price .name").html( $default_price_label );
	  	$(".btn-name").addClass("btn-primary");
	  	$(".btn-price").removeClass("btn-primary");
	  }
	  if ( sortName == "price" ) {
	  	$(".btn-price .name").html( $default_price_label ).append(" ").append(order);
	  	$(".btn-name .name").html( $default_name_label );
	  	$(".btn-price").addClass("btn-primary");
	  	$(".btn-name").removeClass("btn-primary");
	  }
	  $container.isotope({ sortBy : sortName, sortAscending : false });
	  return false;
	});

  $(".sort .default a").click(function(){
	  $container.isotope({ sortBy : "original-order" });
	  $(".btn-price .name").html( $default_price_label );
	  $(".btn-name .name").html( $default_name_label );
	  $(".btn-price").removeClass("btn-primary");
	  $(".btn-name").removeClass("btn-primary");
	  return false;
	});

  //INFINITE SCROLL
  if ( shoestrap_script_vars.infinitescroll == 1 ) {
	 	var $msgText = shoestrap_script_vars.msgText;
   	var $finishedMsg = shoestrap_script_vars.finishedMsg;

    $container.infinitescroll({
			navSelector  : ".pager",
			nextSelector : ".pager .previous a",
			itemSelector : ".type-download",
			loading: {
				msgText: $msgText,
				finishedMsg: $finishedMsg
			}
			// trigger Isotope as a callback
			},function( newElements ) {
					// hide new items while they are loading
					var newElems = $( newElements ).css({ opacity: 0 });
					// ensure that images load before all
					$(newElems).imagesLoaded(function(){
					// show elems now they are ready
					$(newElems).animate({ opacity: 1 });

					if ( shoestrap_script_vars.equalheights == 1 ) 
						$(".product-list .type-download").equalHeights();
					
					$container.isotope( "insert", $(newElems), true );
					$("input .edd-add-to-cart").css("display","none");
					});
				});
  }

  // Increase the total cart quantity in navbar-cart
  $(".edd-add-to-cart").click(function(){
		$("#nav-cart-quantity").html(function(i, val){ return val*1+1 });
	});
	
});