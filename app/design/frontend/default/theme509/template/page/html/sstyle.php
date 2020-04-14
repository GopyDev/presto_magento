<style>
@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);
#cssmenu,
#cssmenu ul,
#cssmenu ul li,
#cssmenu ul li a,
#cssmenu #menu-button {
  margin: 0;
  padding: 0;
  border: 0;
  list-style: none;
  line-height: 1;
  display: block;
  position: relative;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
body
{
   font-family:verdana;
}
#cssmenu:after,
#cssmenu > ul:after {
  content: ".";
  display: block;
  clear: both;
  visibility: hidden;
  line-height: 0;
  height: 0;
}
#cssmenu #menu-button {
  display: none;
}
#cssmenu {
  width: auto;
  border-radius: 5px;
  font-family: 'Open Sans', Helvetica, sans-serif;
  background:#65ae31;
}
#cssmenu.align-center > ul {
  font-size: 0;
  text-align: center;
}
#cssmenu.align-center ul ul {
  text-align: left;
}
#cssmenu.align-center > ul > li {
  display: inline-block;
  float: none;
}
#cssmenu.align-right > ul > li {
  float: right;
}
#cssmenu.align-right ul ul {
  text-align: right;
}
#cssmenu > ul > li {
  float: left;
}
#cssmenu > ul > li > a {
  padding: 10px 6px;
  font-size: 12px;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.25);
  font-weight: 700;
  text-decoration: none;
  -webkit-transition: color .2s ease;
  -moz-transition: color .2s ease;
  -ms-transition: color .2s ease;
  -o-transition: color .2s ease;
  transition: color .2s ease;
}
#cssmenu > ul > li:hover > a,
#cssmenu > ul > li > a:hover,
#cssmenu > ul > li.active > a {
  color: #cae5fd;
}
#cssmenu > ul > li.has-sub > a {
  padding-right: 40px;
}
#cssmenu ul > li.has-sub > a:after {
  content: '';
  position: absolute;
  right: 5px;
  top: 17.5px;
  display: block;
  width: 18px;
  height: 18px;
  border-radius: 9px;
  background:#008000 ;
  background-size: 36px 36px;
  background-position: 0 0;
  background-repeat: no-repeat;
  -webkit-transition: all 0.1s ease-out;
  -moz-transition: all 0.1s ease-out;
  -ms-transition: all 0.1s ease-out;
  -o-transition: all 0.1s ease-out;
  transition: all 0.1s ease-out;
}
#cssmenu ul > li.has-sub:hover > a:after {
  background-position: 0 -18px;
}
#cssmenu ul > li.has-sub > a:before {
  content: '';
  position: absolute;
  right: 11px;
  top: 25.5px;
  display: block;
  width: 0;
  height: 0;
  border: 3px solid transparent;
  border-top-color: #ffffff;
  z-index: 99;
}
#cssmenu ul > li.has-sub:hover > a:before {
  border-top-color: #19799f;
}
#cssmenu ul ul {
  position: absolute;
  left: -9999px;
  opacity: 0;
  -webkit-transition: top .2s ease, opacity .2s ease;
  -moz-transition: top .2s ease, opacity .2s ease;
  -ms-transition: top .2s ease, opacity .2s ease;
  -o-transition: top .2s ease, opacity .2s ease;
  transition: top .2s ease, opacity .2s ease;
}
#cssmenu > ul > li > ul {
  top: 91px;
  padding-top: 8px;
  border-radius: 5px;
}
#cssmenu > ul > li:hover > ul {
  left: auto;
  top: 51px;
  opacity: 1;
}
#cssmenu.align-right > ul > li:hover > ul {
  right: 0;
}
#cssmenu ul ul ul {
  top: 40px;
}
#cssmenu ul ul > li:hover > ul {
  top: 0;
  left: 178px;
  padding-left: 10px;
  opacity: 1;
}
#cssmenu.align-right ul ul > li:hover > ul {
  left: auto;
  right: 178px;
  padding-left: 0;
  padding-right: 10px;
  opacity: 1;
}
#cssmenu ul ul li a {
  width: 180px;
  padding: 12px 25px;
  font-size: 13px;
  font-weight: 700;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.25);
  color: #ffffff;
  text-decoration: none;
  background: ;
  -webkit-transition: color .2s ease;
  -moz-transition: color .2s ease;
  -ms-transition: color .2s ease;
  -o-transition: color .2s ease;
  transition: color .2s ease;
}
#cssmenu ul ul li:hover > a,
#cssmenu ul ul li > a:hover,
#cssmenu ul ul li.active > a {
  color: #cae5fd;
}
#cssmenu ul ul li:first-child > a {
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  box-shadow: inset 0 2px 2px #88d0ed;
}
#cssmenu ul ul li:last-child > a {
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
  box-shadow: inset 0 -3px 0 #27a9de, inset 0 -3px 3px #1f9acc, 0 1px 1px rgba(0, 0, 0, 0.03), 0 2px 2px rgba(0, 0, 0, 0.05), 0 2px 3px rgba(0, 0, 0, 0.13);
}
#cssmenu ul ul > li.has-sub > a:after {
  right: 12px;
  top: 9.5px;
  background: ;
  background: -webkit-linear-gradient(top, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
  background: -ms-linear-gradient(top, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
  background: -moz-linear-gradient(top, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
  background: -o-linear-gradient(top, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
  background: linear-gradient(to bottom, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
  box-shadow: inset 0 -1px 1px #209ed0, inset 0 2px 1px #7fcceb;
  background-size: 36px 36px;
  background-position: 0 0;
  background-repeat: no-repeat;
}
#cssmenu.align-right ul ul > li.has-sub > a:after {
  right: auto;
  left: 12px;
}
#cssmenu ul ul > li.has-sub:hover > a:after {
  background-position: 0 -18px;
}
#cssmenu ul ul > li.has-sub > a:before {
  top: 15.5px;
  right: 16px;
  border-top-color: transparent;
  border-left-color: #ffffff;
}
#cssmenu.align-right ul ul > li.has-sub > a:before {
  top: 15.5px;
  right: auto;
  left: 16px;
  border-top-color: transparent;
  border-right-color: #ffffff;
  border-left-color: transparent;
}
#cssmenu ul ul > li.has-sub:hover > a:before {
  border-top-color: transparent;
  border-left-color: #1c89b5;
}
#cssmenu.align-right ul ul > li.has-sub:hover > a:before {
  border-top-color: transparent;
  border-left-color: transparent;
  border-right-color: #1c89b5;
}
@media all and (max-width: 768px), only screen and (-webkit-min-device-pixel-ratio: 2) and (max-width: 1024px), only screen and (min--moz-device-pixel-ratio: 2) and (max-width: 1024px), only screen and (-o-min-device-pixel-ratio: 2/1) and (max-width: 1024px), only screen and (min-device-pixel-ratio: 2) and (max-width: 1024px), only screen and (min-resolution: 192dpi) and (max-width: 1024px), only screen and (min-resolution: 2dppx) and (max-width: 1024px) {
  #cssmenu {
width: 90%;
}
  #cssmenu ul,
  #cssmenu ul ul,
  #cssmenu ul ul ul,
  #cssmenu > ul,
  #cssmenu.align-center > ul,
  #cssmenu > ul > li > ul,
  #cssmenu > ul > li:hover > ul,
  #cssmenu ul ul li:hover > ul,
  #cssmenu ul ul ul li:hover > ul,
  #cssmenu.align-right ul ul,
  #cssmenu.align-right ul ul li:hover > ul,
  #cssmenu.align-right ul ul ul li:hover > ul {
    position: relative;
    left: 0;
    right: auto;
    top: 0;
    width: 100%;
    display: none;
    padding: 0;
    opacity: 1;
    text-align: left;
  }
  #cssmenu ul li {
    width: 100%;
    border-top: 1px solid rgba(120, 120, 120, 0.2);
  }
  #cssmenu > ul > li > a,
  #cssmenu ul ul li a,
  #cssmenu ul ul li:first-child > a,
  #cssmenu ul ul li:last-child > a {
    width: 100%;
    border-radius: 0;
    box-shadow: none;
    background: none;
  }
  #cssmenu ul li a {
    padding-left: 12.5px;
  }
  #cssmenu ul ul li a {
    padding: 14px 25px 14px 27.5px;
  }
  #cssmenu ul ul ul li a {
    padding-left: 42.5px;
  }
  #cssmenu ul ul ul ul li a {
    padding-left: 57.5px;
  }
  #cssmenu > ul > li.has-sub > a:after,
  #cssmenu > ul > li.has-sub > a:before,
  #cssmenu ul ul li.has-sub > a:after,
  #cssmenu ul ul li.has-sub > a:before {
    display: none;
  }
  #cssmenu #menu-button {
    position: relative;
    display: block;
    padding: 10px;
    padding-left: 12.5px;
    cursor: pointer;
    font-size: 13px;
    color: #ffffff;
    text-shadow: 0 1px 0 rgba(0, 0, 0, 0.25);
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
  }
  #cssmenu .submenu-button {
    position: absolute;
    right: 0;
    display: block;
    width: 53px;
    height: 53px;
    border-left: 1px solid rgba(120, 120, 120, 0.2);
    z-index: 10;
    cursor: pointer;
  }
  #cssmenu ul ul .submenu-button {
    height: 41px;
  }
  #cssmenu ul .submenu-button:after,
  #cssmenu #menu-button:after {
    content: '';
    position: absolute;
    right: 12.5px;
    top: 12.5px;
    display: block;
    width: 28px;
    height: 28px;
    border-radius: 15px;
    background: #008000;
    background: -webkit-linear-gradient(top, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
    background: -ms-linear-gradient(top, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
    background: -moz-linear-gradient(top, #008000 0%, #4ab7e3 25%, #2babde 50%, #008000 75%, #4ab7e3 100%);
    background: -o-linear-gradient(top, #58bde5 0%, #4ab7e3 25%, #2babde 50%, #58bde5 75%, #4ab7e3 100%);
    background: linear-gradient(to bottom, #58bde5 0%, #4ab7e3 25%, #2babde 50%, #58bde5 75%, #4ab7e3 100%);
    box-shadow: inset 0 -1px 1px #209ed0, inset 0 2px 1px #7fcceb;
    background-size: 56px 56px;
    background-position: 0 0;
    background-repeat: no-repeat;
    -webkit-transition: all 0.1s ease-out;
    -moz-transition: all 0.1s ease-out;
    -ms-transition: all 0.1s ease-out;
    -o-transition: all 0.1s ease-out;
    transition: all 0.1s ease-out;
  }
  #cssmenu ul .submenu-button.submenu-opened:after,
  #cssmenu #menu-button.menu-opened:after {
    background-position: 0 -28px;
  }
  #cssmenu ul ul .submenu-button:after {
    top: 6.5px;
  }
  #cssmenu #menu-button:before,
  #cssmenu .submenu-button:before {
    content: '';
    position: absolute;
    right: 22.5px;
    top: 16.5px;
    display: block;
    width: 0;
    height: 0;
    border: 4px solid transparent;
    border-top-color: #ffffff;
    z-index: 99;
  }
  #cssmenu ul ul .submenu-button:before {
    top: 19.5px;
  }
  #cssmenu #menu-button.menu-opened:before,
  #cssmenu .submenu-button.submenu-opened:before {
    border-top-color: #19799f;
  }
}

</style>
      <script>
(function($) {
  $.fn.menumaker = function(options) {

      var cssmenu = $(this), settings = $.extend({
        title: "Menu",
        format: "dropdown",
        sticky: false
      }, options);

      return this.each(function() {


        cssmenu.prepend('<div id="menu-button">' + settings.title + '</div>');
        $(this).find("#menu-button").on('click', function(){
          $(this).toggleClass('menu-opened');
          var mainmenu = $(this).next('ul');
          if (mainmenu.hasClass('open')) {
            mainmenu.hide().removeClass('open');
          }
          else {
            mainmenu.show().addClass('open');
            if (settings.format === "dropdown") {
              mainmenu.find('ul').show();
            }
          }
        });

        cssmenu.find('li ul').parent().addClass('has-sub');

        multiTg = function() {
          cssmenu.find(".has-sub").prepend('<span class="submenu-button"></span>');
          cssmenu.find('.submenu-button').on('click', function() {
            $(this).toggleClass('submenu-opened');
            if ($(this).siblings('ul').hasClass('open')) {
              $(this).siblings('ul').removeClass('open').hide();
            }
            else {
              $(this).siblings('ul').addClass('open').show();
            }
          });
        };

        if (settings.format === 'multitoggle') multiTg();
        else cssmenu.addClass('dropdown');

        if (settings.sticky === true) cssmenu.css('position', 'fixed');

        resizeFix = function() {
          if ($( window ).width() > 768) {
            cssmenu.find('ul').show();
          }

          if ($(window).width() <= 768) {
            cssmenu.find('ul').hide().removeClass('open');
          }
        };
        resizeFix();
        return $(window).on('resize', resizeFix);

      });
  };
})(jQuery);

(function($){
$(document).ready(function(){

$("#cssmenu").menumaker({
   title: "Menu",
   format: "multitoggle"
});


});
})(jQuery);
</script>
<style>
body>.wrapper
{
   background:#fff !important;
   font-weight:bold;
   color:#000;
   overflow:hidden;
}
.page
{
   width:90%;
}
td, th
{
   padding-top:5px !important;
   padding-left:5px !important;
   padding-bottom:5px !important;
   font-weight:bold !important;
}
th
{
    font-weight:bold;
	color:#fff;
	background-color:green;
}
input[type="button"]
{
   margin-top:5px;
    margin-left:5px;
}
body
{
  background:none !important;
}
</style>