jQuery("#all").click(function(){
  jQuery("#block-views-block-gift-guide-block-3, #block-views-block-gift-guide-block-4, #block-views-block-gift-guide-block-5, #block-views-block-gift-guide-block-6").show(1000);
});

jQuery("#for-her").click(function(){
  jQuery("#block-views-block-gift-guide-block-4, #block-views-block-gift-guide-block-5, #block-views-block-gift-guide-block-6").hide(1000);
  jQuery("#block-views-block-gift-guide-block-3").show(1000);
});

jQuery("#for-him").click(function(){
  jQuery("#block-views-block-gift-guide-block-3, #block-views-block-gift-guide-block-5, #block-views-block-gift-guide-block-6").hide(1000);
  jQuery("#block-views-block-gift-guide-block-4").show(1000);
});

jQuery("#new").click(function(){
  jQuery("#block-views-block-gift-guide-block-3, #block-views-block-gift-guide-block-4, #block-views-block-gift-guide-block-6").hide(1000);
  jQuery("#block-views-block-gift-guide-block-5").show(1000);
});

jQuery("#seasonal").click(function(){
  jQuery("#block-views-block-gift-guide-block-3, #block-views-block-gift-guide-block-4, #block-views-block-gift-guide-block-5").hide(1000);
  jQuery("#block-views-block-gift-guide-block-6").show(1000);
});
