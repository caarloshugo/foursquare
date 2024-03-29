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
  
  var config = {
    apiKey: 'J5DRTJ3O5O2Z10SJ4MX4JTMDTGJZWG2LBD0HN44VC23KFKMD',
    authUrl: 'https://foursquare.com/',
    apiUrl: 'https://api.foursquare.com/'
  };
  
 
  //<![CDATA[
 
  /* Attempt to retrieve access token from URL. */
  function doAuthRedirect() {
    var redirect = window.location.href.replace(window.location.hash, '');
    var url = config.authUrl + 'oauth2/authenticate?response_type=token&client_id=' + config.apiKey +
        '&redirect_uri=' + encodeURIComponent(redirect) +
        '&state=' + encodeURIComponent($.bbq.getState('req') || 'users/self');
    window.location.href = url;
  };
 
  if ($.bbq.getState('access_token')) {
    // If there is a token in the state, consume it
    var token = $.bbq.getState('access_token');
    $.bbq.pushState({}, 2)
  } else if ($.bbq.getState('error')) {
  } else {
    doAuthRedirect();
  }
 
  /* HTML 5 geolocation. */
  navigator.geolocation.getCurrentPosition(function(data) {
    var lat = data['coords']['latitude'];
    var lng = data['coords']['longitude'];
    /* Create map. */
    var map = new L.Map('map_canvas')
      .setView(new L.LatLng(lat, lng), 15);
    /**
     * This is a sample map url that you need to change.
     * Sign up at http://mapbox.com/foursquare for a custom map url.
     */
    var mapboxUrl = 'http://a.tiles.mapbox.com/v3/foursquare.map-b7qq4a62.jsonp';
    wax.tilejson(mapboxUrl, function(tilejson) {
      map.addLayer(new wax.leaf.connector(tilejson));
    });
 
    /* Query foursquare API for venue recommendations near the current location. */
    //https://api.foursquare.com/v2/users/USER_ID/checkins
    
    $.getJSON(config.apiUrl + 'v2/venues/explore?ll=' + lat + ',' + lng + '&oauth_token=' + window.token, {}, function(data) {
      
      venues = data['response']['groups'][0]['items'];
    
      /* Place marker for each venue. */
      for (var i = 0; i < venues.length; i++) {
        /* Get marker's location */
        var latLng = new L.LatLng(
          venues[i]['venue']['location']['lat'],
          venues[i]['venue']['location']['lng']
        );
        
        /* Build icon for each icon */
        var leafletIcon = L.Icon.extend({
          iconUrl: venues[i]['venue']['categories'][0]['icon'],
          shadowUrl: null,
          iconSize: new L.Point(32,32),
          iconAnchor: new L.Point(16, 41),
          popupAnchor: new L.Point(0, -51)
        });
        
        // var icon = new leafletIcon();
        /*
        L.marker([40, -75.00], {
			icon: L.icon({
				iconUrl: 'http://placekitten.com/50/50',
				iconSize:     [50, 50], // size of the icon
				iconAnchor:   [25, 25], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, -25]  // point from which the popup should open relative to the iconAnchor
			})
		}).addTo(map);
		*/
		
        var marker = new L.Marker(latLng, {icon: L.icon({ iconUrl: 'images/marker-icon.png', iconSize: [25, 41], iconAnchor: [0, 0], popupAnchor: [0, -25] })})
          .bindPopup(venues[i]['venue']['name'], { closeButton: false })
          .on('mouseover', function(e) { this.openPopup(); })
          .on('mouseout', function(e) { this.closePopup(); });
        map.addLayer(marker);
      }
    })
  })
  //]]>
  </script>
 
  
</head>
<body>
  <div style="width: 100%; height: 100%;" id="map_canvas"></div>
</body>
</html>  
