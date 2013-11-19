$(document).ready(function() {
  maps.initialize();
  search.initialize();
  infoWindows.initialize();
});

var maps = {
  map: null,
  initialize: function() {
    var currentLocation = new google.maps.LatLng(10.315699, 123.885437);
    var options = {
      zoom: 12,
      center: currentLocation
    };
    maps.map = new google.maps.Map(document.getElementById("map-canvas"), options);
    navigator.geolocation.getCurrentPosition(function(position) {
      currentLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
      maps.map.setCenter(currentLocation);
      maps.map.setZoom(5);
    });
    var data = {
      latitude: currentLocation.lat(),
      longitude: currentLocation.lng(),
      content: "<b>You are here</b>"
    };
    maps.plot(data);
  },
  plot: function(data) {
    if (!data.length) {
      data = [data];
    }
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0; i < data.length; i++) {
      var location = new google.maps.LatLng(data[i].latitude, data[i].longitude);
      var marker = new google.maps.Marker({
        position: location,
        map: maps.map,
        title: "<b id='current-position'>You are here</b>"
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
  form: null,
  box: null,
  initialize: function() {
    search.box = $("#search");
    search.form = $("#search-box");
    search.box.keyup(search.maximizeBox).change(search.maximizeBox);
    search.form.submit(search.performSearch);
  },
  maximizeBox: function() {
    if (search.box.val().length > 0) {
      search.box.addClass("maximized");
    } else {
      search.box.removeClass("maximized");
    }
  },
  performSearch: function(e) {
    e.preventDefault();
    if (search.box.val().trim().length > 0) {
      var url = search.form.attr("action");
      var query = search.box.val().trim();
      $.ajax({
        url: url + "/" + query,
        type: "GET",
        success: function(data) {
          data = JSON.parse(data);
          maps.plot(search.format(data));
        }
      });
    }
  },
  format: function(data) {
    var formatted = [];
    for (var i = 0; i < data.length; i++) {
      for (var j = 0; j < data[i].statuses.length; j++) {
        var markerData = {
          latitude: data[i].statuses[j].latitude,
          longitude: data[i].statuses[j].longitude
        };
        
        formatted.push(markerData);
      }
    }
    return formatted;
  }
};

var infoWindows = {
  initialize: function() {
    $(".info-window [data-behavior~=toggle-previous-statuses]").click(function() {
      var previousStatuses = $(this).siblings(".previous-statuses");
      previousStatuses.slideToggle(100).toggleClass("hidden");
      if (previousStatuses.hasClass("hidden")) {
        $(this).text("Show Previous Statuses");
      } else {
        $(this).text("Hide Previous Statuses");
      }
    });
  }
};