$(document).ready(function() {
  maps.initialize();
  search.initialize();
  infoWindows.initialize();
});

var maps = {
  map: null,
  geocoder: null,
  markers: [],
  infoWindow: null,
  infoWindowPlain: null,
  initialize: function() {
    maps.infoWindow = $(".info-window");
    maps.infoWindowPlain = $(".info-window-plain");
    maps.geocoder = new google.maps.Geocoder();

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
      content: "You are here"
    };
    maps.plot(data);
  },
  plot: function(data) {
    if (!data.length) {
      data = [data];
    }
    var bounds = new google.maps.LatLngBounds();
    for (var i = 0; i < data.length; i++) {
      if (data[i].latitude && data[i].longitude) {
        var location = new google.maps.LatLng(data[i].latitude, data[i].longitude);
        var marker = new google.maps.Marker({
          position: location,
          map: maps.map
        });
        maps.markers.push(marker);
        bounds.extend(location);
        maps.enableMarker(marker, data[i]);
      } else {
        (function(data, bounds) {
          maps.geocoder.geocode({address: data.location}, function(results, status) {
            var location = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
            var marker = new google.maps.Marker({
              position: location,
              map: maps.map
            });
            maps.markers.push(marker);
            bounds.extend(location);
            maps.enableMarker(marker, data);
            maps.map.fitBounds(bounds);
          });
        })(data[i], bounds);
      }
    }
    maps.map.fitBounds(bounds);
  },
  enableMarker: function(marker, data) {
    (function(marker, data) {
      google.maps.event.addListener(marker, "click", function(e) {
        var content = data.content;
        if (typeof data.content == "string") {
          maps.fillInfoWindowPlain(data.content);
          content = $(".info-window-plain").clone()[0];
        } else if (typeof data.content == "object") {
          maps.fillInfoWindow(data.content);
          content = $(".info-window").clone()[0];
        }
        var infoBox = new InfoBox({
          content: content,
          disableAutoPan: false,
          maxWidth: 220,
          zIndex: 5,
          infoBoxClearance: 10,
          enableEventPropagation: true
        });
        infoBox.open(maps.map, marker);
        (function(infoBox) {
          google.maps.event.addListener(infoBox, "domready", function() {
            var content = $(infoBox.getContent());
            var closeButton = $(content.siblings("img")[0]);
            content.prepend(closeButton);
            $(content)[0].style.left = -content.outerWidth() / 2 + "px";
          });
        })(infoBox);
      });
    })(marker, data);
  },
  fillInfoWindow: function(data) {
    maps.infoWindow.find("h3").text(data.name);
    maps.infoWindow.find("> .status p").text(data.latestStatus.status);
    maps.infoWindow.find("> .status time").text(displayDate(data.latestStatus.created_at));
    maps.infoWindow.find(".previous-statuses").html("");
    for (var i = 0; i < data.previousStatuses.length; i++) {
      var status = $("<div class='status'>"
                        + "<p>" + data.previousStatuses[i].status + "</p>"
                        + "<time>" + displayDate(data.previousStatuses[i].created_at) + "</time>"
                      + "</div>");
      maps.infoWindow.find(".previous-statuses").append(status);
    }
    if (data.previousStatuses.length > 0) {
      maps.infoWindow.find("[data-behavior~=toggle-previous-statuses]").show();
    } else {
      maps.infoWindow.find("[data-behavior~=toggle-previous-statuses]").hide();
    }
  },
  fillInfoWindowPlain: function(data) {
    maps.infoWindowPlain.find("p").html(data);
  },
  getInfoBoxDimensions: function(content) {
    var dummy = $("<div></div>").html(content);
    $(dummy).appendTo("body").css({"position": "absolute", "left": "-10000px"});
    var dimensions = {width: $(dummy).outerWidth(), height: $(dummy).outerHeight()};
    $(dummy).remove();
    return dimensions;
  },
  clearMarkers: function() {
    for (var i = 0; i < maps.markers.length; i++) {
      maps.markers[i].setMap(null);
    }
    maps.markers = [];
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
          if (data.length > 0) {
            maps.clearMarkers();
            maps.plot(search.format(data));
          } else {
            alert("No Results Found");
          }
        }
      });
    }
  },
  format: function(data) {
    var formatted = [];
    for (var i = 0; i < data.length; i++) {
      var entry = {
        latitude: parseFloat(data[i].statuses[0].latitude),
        longitude: parseFloat(data[i].statuses[0].longitude),
        location: data[i].statuses[0].location,
        content: {
          name: data[i].name,
          latestStatus: data[i].statuses[0],
          previousStatuses: data[i].statuses.slice(1)
        }
      };
      formatted.push(entry);
    }
    return formatted;
  }
};

var infoWindows = {
  initialize: function() {
    $(document).on("click", "[data-behavior~=toggle-previous-statuses]", function() {
      var previousStatuses = $(this).siblings(".previous-statuses");
      previousStatuses.slideToggle(100).toggleClass("hidden");
      if (previousStatuses.hasClass("hidden")) {
        $(this).text("Show Previous Statuses");
      } else {
        $(this).text("Hide Previous Statuses");
      }
    });
  },
  relocateCloseButton: function() {

  }
};





function displayDate(timestamp) {
  var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  var date = timestamp.split(/[- :]/);
  date = new Date(date[0], date[1] - 1, date[2], date[3], date[4], date[5]);
  return months[date.getMonth()] + " " + date.getDate() + ", " + date.getFullYear();
}