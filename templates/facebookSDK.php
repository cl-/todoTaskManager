<?php

includeFacebookSDK();

function includeFacebookSDK(){
  echo('
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=472743636174429";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, \'script\', \'facebook-jssdk\'));</script>
  ');
}

function createFacebookLikeBtn1($url){
  $url = $gethostname();
  $url = '"'.$url.'"';
  echo('
    <div class="fb-like" data-href='.$url.' data-width="300" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
    ');
}

function createFacebookLikeBtn2($divIdName, $url){
  $url = '"'.$url.'"'; //so that we can use either theURL or $url
  echo('
  <script>
    var theURL = window.location.origin; //AUTOMATICALLY get the location origin

    var popupPrompt = document.getElementById("'.$divIdName.'"); //document.createElement("div");
    //popupPrompt.className = "centered popup-prompt";
    //document.getElementsByTagName("body")[0].appendChild(popupPrompt);

    var fbLike = document.createElement("div");
    fbLike.className = "fb-like";
    fbLike.className += " margin-tb5";
    fbLike.style.marginLeft = "40px";
    fbLike.setAttribute("data-href", '. 'theURL' .'); //DONT use JQuery for this. More overhead. http://jsperf.com/jquery-attr-vs-native-setattribute
    fbLike.setAttribute("data-width", "100"); ////using setAttribute to work with DOM attributes that have a dash.
    fbLike.setAttribute("data-show-faces", "true"); //but note setAttribute does not work well with IE. //http://www.quirksmode.org/dom/w3c_core.html#attributes
    //fbLike.setAttribute("data-send", "true");
    fbLike.setAttribute("data-colorscheme", "dark");
  //  FB.XFBML.parse(document.getElementsByTagName(\'fb:like\')[0]);
    popupPrompt.appendChild(fbLike);

    var clearFloatDiv = document.createElement("div");
    clearFloatDiv.className = "floatClear";
    popupPrompt.appendChild(clearFloatDiv);

    var fbPlugins = document.createElement("div");
    popupPrompt.appendChild(fbPlugins);

    var fbFacepile = document.createElement("div");
    fbPlugins.appendChild(fbFacepile);
    var fbFacepileFrame = document.createElement("iframe");
    fbFacepileFrame.src = "http://www.facebook.com/plugins/facepile.php?app_id=220141994808961&colorscheme=dark";
    fbFacepileFrame.scrolling="no";
    fbFacepileFrame.frameBorder="0";
    fbFacepileFrame.style="border:none; overflow:hidden; width:200px;";
    fbFacepileFrame.allowTransparency="true";
    fbFacepileFrame.colorscheme = "dark";
    fbFacepile.appendChild(fbFacepileFrame);
  </script>
  ');
}

?>
