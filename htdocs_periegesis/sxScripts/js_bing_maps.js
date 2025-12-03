function load_BingMapScript() {
    var script = document.createElement("script");
    script.type = "application/javascript";

    // GetMap function will be called when Bing Maps script is downloaded, so inside there initialize your map and other params
    script.src = "https://www.bing.com/api/maps/mapcontrol";

    document.body.appendChild(script);
}

window.onload = load_BingMapScript;


var map;

function loadMapScenario(lat, lng, title) {
    map = new Microsoft.Maps.Map(document.getElementById('js_ModalMapContainer'), {
        //credentials: 'AsMjQKiyvhf9mteQ36Q0ou9TeJhlYDYrVWSdad4tv6200h6khl0xn6dPo69-6mKh',
        credentials: 'Alb9DC3phjbhL2-cRMXZ-7VGetFEBufa1Q9Lf0WFPzzD8l9nR-2Ph2F3QL4Bjww1',
        center: new Microsoft.Maps.Location(lat, lng),
        mapTypeId: Microsoft.Maps.MapTypeId.aerial,
        zoom: 10
    });
    var pushpin = new Microsoft.Maps.Pushpin(map.getCenter(), {
        text: 'P',
        title: title
        //,subTitle: 'Subtitle'
    });
    map.entities.push(pushpin);

    addPins();
    addAutoSuggest();

    function addPins() {
        /* code for adding pins*/
    }

    function addAutoSuggest() {
        /*code for adding auto suggest*/
    }
}
