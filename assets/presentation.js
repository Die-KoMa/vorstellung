function getTileURL(bounds) {
    var res = this.map.getResolution();
    var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
    var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
    var z = this.map.getZoom();
    var limit = Math.pow(2, z);

    if (y < 0 || y >= limit) {
        return OpenLayers.Util.getImagesLocation() + "404.png";
    } else {
        x = ((x % limit) + limit) % limit;
        return this.url + "x=" + x + "&y=" + y + "&z=" + z;    //+ "&ss=" + x + "-" + y + "-" + z
    }
}

function fromEPSG4326(lon, lat) {
    return new OpenLayers.LonLat( lon, lat )
        .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
        );
}

map = new OpenLayers.Map("map", {controls: [new OpenLayers.Control.Attribution()]});
map.addLayer(new OpenLayers.Layer.TMS(
    "OSM Admin Boundaries (experimental)",
    "http://korona.geog.uni-heidelberg.de/tiles/adminb/",
    {
        type: 'png', getURL: getTileURL,
        displayOutsideMaxExtent: true,
        isBaseLayer: true,
        numZoomLevels: 19,
        attribution: "",
        projection: new OpenLayers.Projection("EPSG:900913")
    }
));
if(!geolimits)
    var geolimits = [[5.823, 54.927], [17.117, 45.813]];
var bounds = new OpenLayers.Bounds();
for(var i = 0; i < geolimits.length; i++)
    bounds.extend(fromEPSG4326(geolimits[i][0], geolimits[i][1]));
map.zoomToExtent(bounds, false);

var markers = new OpenLayers.Layer.Markers( "Markers" );
map.addLayer(markers);

var fathom = new Fathom('#presentation', {
    portable: true,
    displayMode: 'multi',
    scrollLength: 300,
    onScrollInterval: 150,
    onActivateSlide: function() {
        var coords = $(this).data('geo');
        if(coords) {
            coords = coords.split(' ');
            var lonLat = fromEPSG4326(coords[1], coords[0]);
            var redDot = new OpenLayers.Icon(
                "assets/reddot.png",
                new OpenLayers.Size(20, 20),
                new OpenLayers.Pixel(-10, -10)
            );
            var marker = new OpenLayers.Marker(lonLat, redDot);
            markers.addMarker(marker);
            $(this).data('marker', marker);
        }
    },
    onDeactivateSlide: function() {
        var marker = $(this).data('marker');
        if(marker)
            markers.removeMarker(marker);
    }
});
$(document).keydown(function(event) {
    var key = event.which;

    if (key === 34 || key == 40) {
        event.preventDefault();
        fathom.nextSlide();
    } else if (key === 33 || key == 38) {
        event.preventDefault();
        fathom.prevSlide();
    }
});
