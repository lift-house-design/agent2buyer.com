

$(document).ready(function() {
  $('#tab1').toggle(function() {
    $('#panel1').animate({
      bottom: '-=120'
    }, 500);
  },function() {
    $('#panel1').animate({
      bottom: '+=120'
    }, 500);
  })
});