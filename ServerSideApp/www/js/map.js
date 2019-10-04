
function showConsumers(map, json) {

    for (var key in json) {

        var coors = [];

        coors.push(new google.maps.LatLng(parseFloat(json[key].latitude_start),parseFloat(json[key].longitude_start)));
        coors.push(new google.maps.LatLng(parseFloat(json[key].latitude_end),parseFloat(json[key].longitude_end)));

        poly = new google.maps.Polyline({
            path: coors,
            geodesic: true,
            strokeColor: '#32a636',
            strokeOpacity: 0.65,
            strokeWeight: 1,
            map: map
        });
    }
}

function showSpans(map, json) {

    for (var key in json) {

        var coors = [];

        coors.push(new google.maps.LatLng(parseFloat(json[key].latitude_start),parseFloat(json[key].longitude_start)));
        coors.push(new google.maps.LatLng(parseFloat(json[key].latitude_end),parseFloat(json[key].longitude_end)));

        poly = new google.maps.Polyline({
            path: coors,
            geodesic: true,
            strokeColor: '#191966',
            strokeOpacity: 0.85,
            strokeWeight: 2,
            map: map
        });
    }
}

function showTPs(map, json) {

    for (var key in json) {

        circle = new google.maps.Circle({
            strokeColor: '#085e0c',
            strokeOpacity: 0.8,
            strokeWeight: 3,
            fillColor: '#32a636',
            fillOpacity: 1.0,
            map: map,
            center: new google.maps.LatLng(parseFloat(json[key].latitude),parseFloat(json[key].longitude)),
            radius: 1
        });
    }
}