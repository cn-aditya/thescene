<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Smart Player API: Basic Setup</title>
</head>

<body>
  <!-- Start of Brightcove Player -->

  <div style="display:none">
  </div>

  <!--
    By use of this code snippet, I agree to the Brightcove Publisher T and C
    found at https://accounts.brightcove.com/en/terms-and-conditions/.
  -->

  <script language="JavaScript" type="text/javascript" src="http://admin.brightcove.com/js/BrightcoveExperiences.js"></script>

  <object id="myExperience922656010001" class="BrightcoveExperience">
   <param name="bgcolor" value="#FFFFFF" />
   <param name="width" value="480" />
   <param name="height" value="270" />
   <param name="playerID" value="12d99739-9e51-4fd5-a770-4574ef390cf7" />
   <param name="playerKey" value="AQ~~,AAAA1oy1bvE~,ALl2ezBj3WHB4SZjVHPI3HSdWBlOCXX4" />
   <param name="isVid" value="true" />
   <param name="isUI" value="true" />
   <param name="dynamicStreaming" value="true" />

   <param name="@videoPlayer" value="4472137462001" />

   <!-- smart player api params -->
   <param name="includeAPI" value="true" />
   <param name="templateLoadHandler" value="onTemplateLoad" />
   <param name="templateReadyHandler" value="onTemplateReady" />

  </object>

  <!--
    This script tag will cause the Brightcove Players defined above it to be created as soon
    as the line is read by the browser. If you wish to have the player instantiated only after
    the rest of the HTML is processed and the page load is complete, remove the line.
  -->
  <script type="text/javascript">brightcove.createExperiences();</script>

  <!-- End of Brightcove Player -->

  <script type="text/JavaScript">
    var player,
    APIModules,
    videoPlayer;
    function onTemplateLoad(experienceID){
     player = brightcove.api.getExperience(experienceID);
     APIModules = brightcove.api.modules.APIModules;
    }
    function onTemplateReady(evt){
     videoPlayer = player.getModule(APIModules.VIDEO_PLAYER);
     videoPlayer.play();
    }
  </script>

</body>
</html>