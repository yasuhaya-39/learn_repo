(function(){
  'use strict';
  var logo_menu_logo  = document.getElementsByClassName('logo_menu_logo');
  var logo_menu_list  = document.getElementsByClassName('logo_menu_list');
  var room_img        = document.getElementsByClassName('room_img');
  var custom_text_0   = document.getElementsByClassName('custom_text_0');
  var logo_menu_icon  = document.getElementsByClassName('logo_menu_icon');

  var body_tag        = document.getElementById('body_tag');
  var html_tag        = document.getElementById('html_tag');

  function init() {
    logo_menu_logo[0].addEventListener('click', function() {
      logo_menu_list[0].classList.toggle('disable');
      logo_menu_list[0].classList.toggle('enable');
      body_tag.classList.toggle('height_max');
      html_tag.classList.toggle('height_max');
      body_tag.classList.toggle('overflow_hidden');
      room_img[0].classList.toggle('logo_menu_disp');
      custom_text_0[0].classList.toggle('disable');
      logo_menu_logo[0].classList.toggle('logo_menu_logo_black');
      logo_menu_icon[0].classList.toggle('rotate_180');

    });
  }

  init();

})();
