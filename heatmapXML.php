<!-- reference: 
https://developers.google.com/maps/documentation/javascript/examples/layer-heatmap
-->

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Heatmaps</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto','sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }
      #floating-panel {
        background-color: #fff;
        border: 1px solid #999;
        left: 25%;
        padding: 5px;
        position: absolute;
        top: 10px;
        z-index: 5;
      }
    </style>
  </head>

  <body>
    <div id="floating-panel">
      <button onclick="toggleHeatmap()">Toggle Heatmap</button>
      <button onclick="changeGradient()">Change gradient</button>
      <button onclick="changeRadius()">Change radius</button>
      <button onclick="changeOpacity()">Change opacity</button>
    </div>
    <div id="map"></div>


    <?php
    // check if the form was submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // check if the file was uploaded without errors
        if(isset($_FILES["file"]) && $_FILES["file"]["error"] == 0){
            $allowed = array("csv" => "text/csv", "xml" => "text/xml");
            $filename = $_FILES["file"]["name"];
            $filetype = $_FILES["file"]["type"];
            $filesize = $_FILES["file"]["size"];
        
            // verify file extension
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
        
            // verify file size - 88MB maximum
            $maxsize = 88 * 1024 * 1024;
            if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
        
            // verify the type of the file
            if(in_array($filetype, $allowed)){
                  move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
                  echo "Your file was uploaded successfully.";
                } else{
                    echo "Error: There was a problem uploading your file. Please try again."; 
                  }
            } else{
            echo "Error: " . $_FILES["file"]["error"];
              }
        }

       $file = array();
       // to read all data from the uploaded file
       $xml=simplexml_load_file($_FILES["file"]["tmp_name"]);

       // to filter data and store geographical data in a new array 
       foreach($xml->children() as $_POST["field1"]) { 
       array_push($file,$_POST["field1"]->{$_POST["field2"]});
       }
    ?>

    <script type="text/javascript">
    // This example requires the Visualization library. Include the libraries=visualization

      var map, heatmap

      function initMap() {   
         var arr = [];
         
         // to pass geographical data's array from PHP to JS
         var php_var = <?php echo json_encode($file); ?>;

         // to process and transform the data 
         for (i in php_var){ 
         var out = php_var[i][0].slice(1,-1).split(",");
         arr.push(new google.maps.LatLng(out[0],out[1]));
      }

      var centervalue = arr[0];

      var myoptions = {
          zoom: 11,
          center: centervalue, 
          mapTypeId: 'satellite'
      }; 

      map = new google.maps.Map(document.getElementById('map'), myoptions);

        heatmap = new google.maps.visualization.HeatmapLayer({
          data: arr,
          map: map
        });
      }

      function toggleHeatmap() {
        heatmap.setMap(heatmap.getMap() ? null : map);
      }

      function changeGradient() {
        var gradient = [
          'rgba(0, 255, 255, 0)',
          'rgba(0, 255, 255, 1)',
          'rgba(0, 191, 255, 1)',
          'rgba(0, 127, 255, 1)',
          'rgba(0, 63, 255, 1)',
          'rgba(0, 0, 255, 1)',
          'rgba(0, 0, 223, 1)',
          'rgba(0, 0, 191, 1)',
          'rgba(0, 0, 159, 1)',
          'rgba(0, 0, 127, 1)',
          'rgba(63, 0, 91, 1)',
          'rgba(127, 0, 63, 1)',
          'rgba(191, 0, 31, 1)',
          'rgba(255, 0, 0, 1)'
        ]
        heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
      }

      function changeRadius() {
        heatmap.set('radius', heatmap.get('radius') ? null : 20);
      }

      function changeOpacity() {
        heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);
      }

    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC_EpePZ9WMbwn3IpecRrSjp3QVLSbnPMA&libraries=visualization&callback=initMap">
    </script>
  </body>
</html>