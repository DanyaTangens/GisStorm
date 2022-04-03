let currentCoupling = null
let coupleList = []
let currentCouplingFeature = null;
var test
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
    if (currentTool === 0) {
        console.log("You clicked the map at " + e.latlng)
    } else if (currentTool === 2) {
        marker = new L.Marker([e.latlng.lat, e.latlng.lng], {icon: getIcon(currentTool)}).addTo(map)
        currentCoupling = marker
        document.getElementById('coupling_nomination').value = '';
        document.getElementById('coupling_type').value = '';
        document.getElementById('coupling_sl_txt').value = '';

        document.getElementById('properties1').click();
    }
}

async function getCoupling() {
    if (!isZoom) {
        return;
    }
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
                        return coupleList.indexOf(parseInt(feature.properties.id)) === -1;
                    }
                })
                    .on('click', markerOnClick)
                    .on('mousedown', function (feature) {
                        currentCouplingFeature = feature
                    })
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

    if (coupleSettingsChange === 0) {
        obj.action = 'POST'
        obj.id = 0
        obj.lat = currentCoupling._latlng.lat
        obj.lng = currentCoupling._latlng.lng
    } else {
        obj.action = 'PUT'
        obj.id = coupleSettingsId
        coupleSettingsChange = 0
        obj.lat = currentCouplingFeature.latlng.lat
        obj.lng = currentCouplingFeature.latlng.lng
    }

    obj.name = document.getElementById('coupling_nomination').value.trim()
    obj.type = document.getElementById('coupling_type').value.trim()
    obj.comment = document.getElementById('coupling_sl_txt').value.trim()

    saveDataCoupling(obj)
}

async function saveDataCoupling(obj) {
    let response = await fetch(`./api/v1/couplings`, {
        method: obj.action, headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json'
        }, body: JSON.stringify({
            id: obj.id, name: obj.name, type_coupling: obj.type, description: obj.comment, lat: obj.lat, lng: obj.lng,
        })
    });

    let data = await response.json();
    if (data.result !== null) {
        document.getElementById('osx_couple_close_btn').click()
        currentTool = 0
        if (obj.action === 'PUT') {
            updatePopupMarker(data)
        }
    }
}

function coupleDelete(id) {
    if (!confirm('Вы уверены что хотите удалить муфту?')) {
        return;
    }
    fetch('./api/v1/couplings/' + id, {
        method: 'DELETE', headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json'
        }
    })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            if (typeof data.error !== 'undefined') {
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

function coupleMoveUpdate(obj) {
    fetch(`./api/v1/couplings/${obj.id}/move`, {
        method: 'PUT', headers: {
            'Accept': 'application/json', 'Content-Type': 'application/json'
        }, body: JSON.stringify({
            id: obj.id, lat: obj.lat, lng: obj.lng,
        })
    })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            if (typeof data.error !== 'undefined') {
                alert(data.error)
            } else {
                updatePopupMarker(data)
            }
        })
        .catch(function (reason) {
            console.log(reason);
        });
}

/**
 * Установка глобальных значений для редактирования муфты
 * @param id
 */
function coupleSettings(id) {
    coupleSettingsChange = 1;
    coupleSettingsId = id;
    document.getElementById('properties1').click();
}

/**
 * При клике на существующий объект, подготавливаем форму в соответствии с его данными,
 * чтобы в случае чего обновить информацию
 * @param feature
 */
function markerOnClick(feature)
{
    obj = feature.layer.feature.properties.obj;
    if(obj === 2){
       document.getElementById('coupling_nomination').value = feature.layer.feature.properties.name;
        document.getElementById('coupling_type').value = feature.layer.feature.properties.type_coupling;
        document.getElementById('coupling_sl_txt').value = feature.layer.feature.properties.description;
        currentCouplingFeature = feature;
    }
}

function updatePopupMarker(data) {
    if (data.result.obj === 2) {
        currentCouplingFeature.layer.bindPopup(
            `<b>Информация о муфте:</b><br>
            ID: ${data.result.id}<br>
            Наименование муфты: ${data.result.name == null ? 'нет' : data.result.name}<br> 
            Тип: ${data.result.type_coupling === 0 ? "Прямая муфта" : "Разветвительная муфта"}<br>
            Доп. инфо: ${data.result.description == null ? 'нет' : data.result.description}<br> 
            Текущие координаты: ${data.result.lat + ',' + data.result.lng}<br>
            <input type=submit id=couple_settings onclick='coupleSettings(${data.result.id});' value='Изменить параметры'>&nbsp;
            <input type=submit id=couple_delete onclick='coupleDelete(${data.result.id});' value='Удалить'>&nbsp;<br>
            `
        );
    }
}