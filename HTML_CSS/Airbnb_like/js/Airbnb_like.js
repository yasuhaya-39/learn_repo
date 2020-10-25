(function(){
  'use strict';
  var arrow_menu_list = document.getElementsByClassName('arrow_menu_list');
  var check_box       = document.getElementById('check_box');
  var footer_tag       = document.getElementsByTagName('footer');
  var main_area       = document.getElementsByClassName('main_area');
  var arrow_menu_area = document.getElementsByClassName('arrow_menu_area');
  var card_ent_1      = document.getElementsByClassName('card_ent_1');
  var card_ent_2 = document.getElementsByClassName('card_ent_2');
  var feature_area = document.getElementsByClassName('feature_area');
  var menu_area_2 = document.getElementsByClassName('menu_area_2');
  var footer_area = document.getElementsByClassName('footer_area');

  var search_icon = document.getElementsByClassName('search_icon');
  var search_text = document.getElementsByClassName('search_text');
  var cancel_btn = document.getElementsByClassName('cancel_btn');

  var timer_id_display_none;
  var timer_id_cancel_btn;

  /* タイムアウト関数 */
  function display_none() {
    card_ent_1[0].classList.add('Inactive');
    card_ent_2[0].classList.add('Inactive');
    feature_area[0].classList.add('Inactive');
    menu_area_2[0].classList.add('Inactive');
    footer_area[0].classList.add('Inactive');

    clearInterval(timer_id_display_none);
  }

  function cancel_btn_active() {
    cancel_btn[0].classList.add('cancel_btn_active');
    clearInterval(timer_id_cancel_btn);
  }

  function init() {
    check_box.addEventListener('click', function() {
      if(check_box.checked === true) {
        arrow_menu_list[0].classList.add('arrow_menu_list_active');
        // card_ent_1[0].classList.add('Inactive');
        // card_ent_2[0].classList.add('Inactive');
        // feature_area[0].classList.add('Inactive');
        // menu_area_2[0].classList.add('Inactive');
        // footer_area[0].classList.add('Inactive');
        // arrow_menu_area[0].classList.add('menu_list_size');
        main_area[0].classList.add('menu_list_size');
        timer_id_display_none = setInterval(display_none, 300);

      } else {
        arrow_menu_list[0].classList.remove('arrow_menu_list_active');
        card_ent_1[0].classList.remove('Inactive');
        card_ent_2[0].classList.remove('Inactive');
        feature_area[0].classList.remove('Inactive');
        menu_area_2[0].classList.remove('Inactive');
        footer_area[0].classList.remove('Inactive');
        // arrow_menu_area[0].classList.remove('menu_list_size');
        main_area[0].classList.remove('menu_list_size');
      }
    });

    search_text[0].addEventListener('focus', function() {
      var intViewportWidth = window.innerWidth;

      if(intViewportWidth < 745) {
        timer_id_cancel_btn = setInterval(cancel_btn_active, 300);
      }
    });

    search_text[0].addEventListener('blur', function() {
      var intViewportWidth = window.innerWidth;

      if(intViewportWidth < 745) {
        cancel_btn[0].classList.remove('cancel_btn_active');
      }
    });
  }

  init();

})();
