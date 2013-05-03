<html>
<head>
  <title>foursquare :: Explore Sample</title>
  
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" id="jquery"></script>
  
 
  <link href="styles/leaflet.css" type="text/css" rel="stylesheet" />
  <link href="styles/apisamples.css" type="text/css" rel="stylesheet" />
 
  <script src="scripts/apisamples.js" type="text/javascript"></script>
  <script src="scripts/jquery.ba-bbq.js" type="text/javascript"></script>
  <script src="scripts/leaflet.js" type="text/javascript"></script>
  <script src="scripts/wax.leaf.min.js" type="text/javascript"></script>
 
  <style type="text/css">
    html { height: 100%; }
    body { height: 100%; margin: 0; padding: 0; }
  </style>
 
  <script type="text/javascript">
	/**
         * Sample application that uses the foursquare API, Leaflet, and the MapBox API
         * to browse a user's history on a map.
         */
        function HistoryBrowse(apiKey, authUrl, apiUrl, mapboxId) {
          this.foursquare = new Foursquare(apiKey, authUrl, apiUrl);
          this.selectedCategory = null;
          this.map = new L.Map('map')
            .setView(new L.LatLng(8.537565350804018, -115.57617187499999), 14);
          var map = this.map;
          var https = (location.protocol === 'https:'),
                      base_https = 'https://dnv9my2eseobd.cloudfront.net/v3/',
                      base = 'http://a.tiles.mapbox.com/v3/',
                      mapboxUrl = (https ? base_https : base) + mapboxId + '.jsonp';
          wax.tilejson(mapboxUrl, function(tilejson) {
            map.addLayer(new wax.leaf.connector(tilejson));
          });
          this.map.on('move', bind(this.draw, this));
        }

        /**
         * Fetch user's data and display it on the map.
         */
        HistoryBrowse.prototype.run = function() {
          this.foursquare.venueHistory(bind(this.onHistory, this));
        };

        /**
         * Return true if the array of categories contains the selected category.
         * @private
         */
        HistoryBrowse.prototype.categoryMatch = function(categories) {
          if (!this.selectedCategory) {
            return true;
          }
          for (var i = 0; i < categories.length; i++) {
            if (categories[i].id == this.selectedCategory) {
              return true;
            }
          }
          return false;
        };

        /**
         * Render list of places in specified category and map area.
         * @private
         */
        HistoryBrowse.prototype.draw = function() {
          if (!this.history) { return }
          var bounds = this.map.getBounds();
		  console.log(bounds);
		  
          var html = [], visitCount = 0, placeCount = 0;
          
          for(var i = 0; i < this.history.length; i++) {
            var entry = this.history[i]['venue'];
            
            var latLng = new L.LatLng(entry.location.lat, entry.location.lng);
        
            if(bounds.contains(latLng) && this.categoryMatch(entry.categories)) {
              placeCount++;
              visitCount += this.history[i].beenHere;
              html.push('<a href="http://foursquare.com/venue/', entry['id'], '">', entry['name'], '</a> (' + this.history[i].beenHere + ' visits)<br>');
            }
            
          }
          
          var header = '<span class="count">' + visitCount + '</span> visits to <span class="count">' + placeCount + '</span> places<br/><br/>';
          $('#content').html(header + html.join(''));
          
        }
		
		
		/**
         * Render mareker of places in specified category and map area with.
         * @private
         */
        HistoryBrowse.prototype.markers = function() {
          if (!this.history) { return }
          
          var bounds = this.map.getBounds();

          for(var i = 0; i < this.history.length; i++) {
            var entry = this.history[i]['venue'];
            
            var latLng = new L.LatLng(entry.location.lat, entry.location.lng);
			
			var marker = new L.Marker(latLng, {icon: L.icon({ iconUrl: 'images/marker-icon.png', iconSize: [25, 41], iconAnchor: [0, 0], popupAnchor: [0, -25] })})
			  .bindPopup(entry['name'], { closeButton: false })
			  .on('mouseover', function(e) { this.openPopup(); })
			  .on('mouseout', function(e) { this.closePopup(); });
			this.map.addLayer(marker);
          }
        }
        
        /**
         * Build up category select box from history.
         * @private
         */
        HistoryBrowse.prototype.buildCategoryList = function() {
          // Find categories and build drop down
          var categories = {};
          for (var i = 0; i < this.history.length; i++) {
            var entryCategories = this.history[i]['venue']['categories'];
            for (var j = 0; j < entryCategories.length; j++) {
              var category = entryCategories[j];
              categories[category['id']] = category['name'];
            }
          }
          $('#category').append('<option value="">All</option>');
          for (var category in categories) {
            $('#category').append('<option value="' + category + '">' + categories[category] + '</option>');
          }
          $('#category').change(bind(function() {
            this.selectedCategory = $('#category').find('option:selected').val();
            this.draw();
          }, this));
        };

        /**
         * Given the response from a venue history request, build a list of all visited
         * categories and save the history for rendering.
         * @private
         */
        HistoryBrowse.prototype.onHistory = function(history) {
          this.history = history;
          this.markers();
          this.buildCategoryList();
          this.draw();
        }

        //]]>
        $(function() {
        
          new HistoryBrowse(
            'J5DRTJ3O5O2Z10SJ4MX4JTMDTGJZWG2LBD0HN44VC23KFKMD',
            'https://foursquare.com/',
            'https://api.foursquare.com/',
            /**
             * This is a sample map url that you need to change.
             * Sign up at http://mapbox.com/foursquare for a custom map url.
             */
            'foursquare.map-b7qq4a62').run();
        
      })
  </script>
 
  
</head>
<body>
	<div id="map" style="width: 600px; height: 600px; position: absolute;"></div>
	<div id="content" style="margin-left: 610px">
		<h1>What's hot right now?</h1>
		Hover over the markers in an area to see what's trending.
	</div>
</body>
</html>  
