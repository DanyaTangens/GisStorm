let current_coupling = null
let couple_list = []
let current_coupling_feature = null;

function getIcon(number) {
    if (number === 2) {
        return L.icon({
            iconUrl: '/public/images/icon/Coupling.png',
            iconAnchor: [15, 15],
            popupAnchor: [0, 0],
            tooltipAnchor: [0, 0]
        });
    }
}

function onMapClick(e) {
    let marker
    if (current_tool === 0) {
        console.log("You clicked the map at " + e.latlng)
    } else if (current_tool === 2) {
        marker = new L.Marker([e.latlng.lat, e.latlng.lng], {icon: getIcon(current_tool)}).addTo(map)
        current_coupling = marker
        document.getElementById('coupling_nomination').value = '';
        document.getElementById('coupling_type').value = '';
        document.getElementById('coupling_sl_txt').value = '';
        document.getElementById('coupling_folder').value = '';

        document.getElementById('properties1').click();
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
        sw_lat: southWest.lat, sw_lng: southWest.lng, ne_lat: northEast.lat, ne_lng: northEast.lng
    }
    let url = './api/v1/couplings?' + new URLSearchParams(params).toString();

    fetch(url)
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            if (data.error !== null) {
                alert(data.error)
            } else {
                L.geoJSON(data.result, {
                    pointToLayer: pointToLayer, onEachFeature: onEachFeature, filter: function (feature, layer) {
                        return couple_list.indexOf(parseInt(feature.properties.id)) === -1;
                    }
                })
                    .on('click', markerOnClick)
                    .addTo(map)
            }
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

    saveDataCoupling(obj);
}

async function saveDataCoupling(obj) {
    let response = await fetch('./api/v1/couplings', {
        method: obj.action, headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json'
        }, body: JSON.stringify({
            id: obj.id, name: obj.name, type_coupling: obj.type, description: obj.comment, lat: obj.lat, lng: obj.lng,
        })
    });

    let data = await response.json(); // читаем ответ в формате JSON
    if (data.result !== null) {
        document.getElementById('osx_couple_close_btn').click()
        current_tool = 0
        alert(data.result)
    }
}

function couple_delete(id) {
    if (!confirm('Вы уверены что хотите удалить муфту?')) {
        return;
    }
    let response = fetch('./api/v1/couplings/' + id, {
        method: 'DELETE', headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json'
        }
    })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            if (data.error === null) {
                alert(data.error)
            } else {
                map.eachLayer(function (layer) {
                    if (layer instanceof L.Marker) {
                        if (layer.feature.properties.id === id) {
                            map.removeLayer(layer)
                        }
                    }
                });
            }
        })
        .catch(function (reason) {
            console.log(reason);
        });
}

function couple_settings(id) {
    couple_settings_change = 1;
    couple_settings_id = id;
    document.getElementById('properties1').click();
}

function markerOnClick(feature)
{
    console.log(feature);
    obj = feature.layer.feature.properties.obj;
    console.log(obj)
    if(obj === 2){
       document.getElementById('coupling_nomination').value = feature.layer.feature.properties.name;
        document.getElementById('coupling_type').value = feature.layer.feature.properties.type_coupling;
        document.getElementById('coupling_sl_txt').value = feature.layer.feature.properties.description;
        current_coupling_feature = feature;
    }
}