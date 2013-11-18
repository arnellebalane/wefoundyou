$(document).ready(function() {
  maps.initialize();
  search.initialize();
});

var maps = {
  map: null,
  initialize: function() {
    var currentLocation = new google.maps.LatLng(11.3333, 123.0167);
    var options = {
      zoom: 6,
      center: currentLocation,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    maps.map = new google.maps.Map(document.getElementById("map-canvas"), options);
    var data = {
      latitude: 11.1234,
      longitude: 123.1234,
      content: "<b>You are here</b>"
    };
    maps.plot(data);
  },
  plot: function(data) {
    if (typeof data != "object") {
      data = [data];
    }
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0; i < data.length; i++) {
      var location = new google.maps.LatLng(data[i].latitude, data[i].longitude);
      var marker = new google.maps.Marker({
        position: location,
        map: maps.map,
        title: "Lorem Ipsum"
      });
      var infoWindow = new google.maps.InfoWindow({
        content: data[i].content
      });
      bounds.extend(location);

      (function(marker, infoWindow) {
        google.maps.event.addListener(marker, "click", function(e) {
          infoWindow.open(maps.map, marker);
        });
      })(marker, infoWindow);
    }
    maps.map.fitBounds(bounds);
  }
};

var search = {
  box: null,
  initialize: function() {
    search.box = $("#search");
    search.box.keyup(search.maximizeBox).change(search.maximizeBox);
  },
  maximizeBox: function() {
    if (search.box.val().length > 0) {
      search.box.addClass("maximized");
    } else {
      search.box.removeClass("maximized");
    }
  }
};