let current_coupling = null;

function getIcon(number) {
    if (number === 1) {
        return L.icon({
            iconUrl: '/public/images/icon/Coupling.png',
            iconAnchor: [15, 30],
            popupAnchor: [0, 0],
            tooltipAnchor: [0, 0]
        });
    }
}

function onMapClick(e) {
    if (current_tool === 2) {
        marker = new L.Marker([e.latlng.lat, e.latlng.lng], {icon: getIcon(1)}).addTo(map);
        current_coupling = marker;
        document.getElementById('coupling_nomination').value = '';
        document.getElementById('coupling_type').value = '';
        document.getElementById('coupling_sl_txt').value = '';
        document.getElementById('coupling_folder').value = '';

        document.getElementById('properties1').click();
    }
    if (current_tool === 0) {
        console.log("You clicked the map at " + e.latlng);
    }
}

async function get_coupling() {
    // if (!zoom_load_data) {
    //     return;
    // }
    let bounds = map.getBounds();
    let southWest = bounds.getSouthWest();
    let northEast = bounds.getNorthEast();

    let params = {
        sw_lat: southWest.lat,
        sw_lng: southWest.lng,
        ne_lat: northEast.lat,
        ne_lng: northEast.lng
    }
    let url = './api/v1/couplings?' + new URLSearchParams(params).toString();
    console.log(url);
    fetch(url)
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            console.log(data);
            L.geoJSON(data, {
                pointToLayer: pointToLayer,
                onEachFeature: onEachFeature,
                // filter: function(feature, layer){
                //     return couple_list.indexOf(parseInt(feature.properties.id)) == -1;
                // }
            })
                // .on('click', markerOnClick)
                .addTo(map)
        });
}

function updateDataCoupling() {
    let obj = {'id': 0, 'action': '', 'name': '', 'type': 0, 'lat': 0, 'lng': 0, 'obj': 2};

    if (document.getElementById('coupling_nomination').value.trim() === '') {
        alert('Название не может быть пустым');
        return;
    }

    if (couple_settings_change === 0) { // insert
        obj.action = 'POST';
        obj.id = 0;
        obj.lat = current_coupling._latlng.lat;
        obj.lng = current_coupling._latlng.lng;
    } else {
        obj.action = 'PUT';
        obj.id = couple_settings_id;
        couple_settings_change = 0;
    }

    obj.name = document.getElementById('coupling_nomination').value.trim();
    obj.type = document.getElementById('coupling_type').value.trim();
    obj.comment = document.getElementById('coupling_sl_txt').value.trim();
    obj.link = document.getElementById('coupling_folder').value.trim();

    saveDataCoupling(obj);
}

async function saveDataCoupling(obj) {
    let response = await fetch('./api/v1/couplings', {
        method: obj.action,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: obj.name,
            type: obj.type,
            comment: obj.comment,
            link: obj.link,
            lat: obj.lat,
            lng: obj.lng,
        })
    });

    let data = await response.json(); // читаем ответ в формате JSON
    alert(data);
}

async function test() {
    let response = await fetch('./test', {
        method: 'post',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            title: 'Beispielprojekt',
            url: 'http://www.example.com',
        })
    });

    let commits = await response.json(); // читаем ответ в формате JSON

    alert(commits.title);
}