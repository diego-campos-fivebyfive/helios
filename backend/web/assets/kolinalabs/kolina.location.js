/*
 * kolina.location.js v1.5
 * http://kolinalabs.com
 * @author Claudinei Machado <cjchamado@gmail.com>
 *
 * Copyright 2014, Claudinei Machado <claudinei@kolinalabs.com>
 * Released under the MIT License.
 *
 * * Generate Google Maps With API v3
 * * Get Geocode Information By Address
 * * Get Address Information By Postcode [republica virtual:http://cep.republicavirtual.com.br/]
 */

var currentSettings = {
    zoom: 3,
    element: "map-canvas",
    marker: true,
    lat: -22.070647,
    lng: -48.4337
};

function mergeSettings(settings) {
    currentSettings = $.extend(true, currentSettings, settings);
    return currentSettings;
}

function createLog(message) {
    console.log(message);
}

function getAddressByPostCode(postcode, callback) {
    if (postcode) {
        $.ajax({
            type: 'post',
            url: 'http://cep.republicavirtual.com.br/web_cep.php?cep=' + postcode + '&formato=jsonp',
            data: 1,
            success: function(response) {
                callback.success(response);
            },
            error: function(response) {
            },
            beforeSend: function() {
            }
        });
    }
}

/**
 * address format: Rua+das+Oliveiras+Centro+Guarapuava,PR+1340
 */
function getGeocode(address, callback) {
    if (typeof (address) == 'string' && address.length > 5) {
        $.ajax({
            url: "http://maps.googleapis.com/maps/api/geocode/json?address=" + address + "&sensor=false",
            type: "get",
            success: function(response) {
                callback.success(response);
            }
        });
    }
}

function createGoogleMapsConfig(geocode, callback) {
    if(geocode.results.length) {
        var results = geocode.results[0],
            geometry = results.geometry,
            lat = geometry.location.lat,
            lng = geometry.location.lng;

        var settings = mergeSettings({
            lat: lat,
            lng: lng,
            zoom: 16
        });

        if (callback) {
            callback.success(settings);
        }
    }
}

function createGoogleMaps(settings) {
    mergeSettings(settings);

    var address = settings.address,
      lat = settings.lat,
      lng = settings.lng;

    if (lat && lng) {
        if (window.google && window.google.maps) {

            var element = document.getElementById(settings.element),
              zoom = settings.zoom,
              center = new google.maps.LatLng(lat, lng),
              options = {
                  zoom: zoom,
                  center: center
              },
            gmap = new google.maps.Map(element, options),
              marker = settings.marker;


            var Gmarker = new google.maps.Marker({
                position: center,
                map: gmap,
                title: marker.title
            });

        } else {
            createLog("google maps api not loaded");
        }
    } else {
        if ("string" == typeof (address) && address.length > 0) {
            getGeocode(address, "createGoogleMapsConfig(response,'createGoogleMaps(settings)')");
        }
    }
}