let map;

let isZoom = true;

let coupleSettingsChange = 0;
let coupleSettingsId = null;
let currentTool = 0;

document.addEventListener("DOMContentLoaded", () => {
    map = L.map('map').setView([56.8383, 60.6031], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    map.on('click', onMapClick)

    map.on('zoomend', function () {
        console.log('zoom: ' + map.getZoom());
        if (map.getZoom() <= 11) {
            isZoom = false
            coupleList = []

            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer)
                }
            });
        }
        isZoom = true
    })

    map.on('dragend', function() {
        console.log("movemap")
        //$('#loader_countdown_div').css('display', 'block'); Здесь индикатор загрузки
        // if (dragendCountdown < 10000) {
        //     clearInterval(dragendTimer);
        // }
        //
        // dragendCountdown = 4500;
        // dragendTimer = setInterval(dragend_func, dragend_interval);
        //
        // var c = map.getCenter();
        // localStorage.setItem('lat', c.lat);
        // localStorage.setItem('lon', c.lng);
    });


    getCoupling()
});


function setTool(id) {
    switch (id) {
        case 0:
            currentTool = 0;
            break;
        case 1:
            currentTool = 1;
            break;
        case 2:
            currentTool = 2;
            coupleSettingsChange = 0;
            break;
        default:
            currentTool = 0;
            break;
    }
}

//функция, которая вызывается при отрисовке
function pointToLayer(feature, latLng) {
    return L.marker(latLng, {icon: leafletIcon(feature)});
}

function leafletIcon(feature) {
    return L.icon({
        iconUrl: '/public/images/icon/Coupling.png',
        iconAnchor: [15, 15],
        popupAnchor: [0, 0],
        tooltipAnchor: [0, 0]
    });
}

function onEachFeature(feature, layer) {

    if (feature.properties.obj === 2) {
        let node_type = '';
        if (feature.properties.type_coupling === 0) {
            node_type = "Прямая муфта";
        } else if (feature.properties.type_coupling === 1) {
            node_type = "Разветвительная муфта";
        }

        coupleList.push(parseInt(feature.properties.id));

        let id = feature.properties.id; //айдишник
        let name = feature.properties.name; // наименование копируемого
        let description = feature.properties.description; // описание
        let lat = feature.geometry.coordinates[1]; // какая-то координата
        let lng = feature.geometry.coordinates[0]; // какая-то координа, но вторая

        layer.options.draggable = true;
        layer.on('dragend', function (event) {
            changePositionObject(event);
        });
        layer.bindPopup(
            `<b>Информация о муфте:</b><br>
            ID: ${id}<br>
            Наименование муфты: ${name == null ? 'нет' : name}<br> 
            Тип: ${node_type}<br>
            Доп. инфо: ${description == null ? 'нет' : description}<br> 
            Текущие координаты: ${lat + ',' + lng}<br>
            <input type=submit id=couple_settings onclick='coupleSettings(${id});' value='Изменить параметры'>&nbsp;
            <input type=submit id=couple_delete onclick='coupleDelete(${id});' value='Удалить'>&nbsp;<br>
            `
        );
    }
}

function changePositionObject(e) {
    console.log(e)
    if (e.target.feature.properties.obj === 2) {
        let obj = {'id': 0, 'action': 'put', 'lat': 0, 'lng': 0};
        obj.id = parseInt(e.target.feature.properties.id);
        obj.lat = e.target._latlng.lat;
        obj.lng = e.target._latlng.lng;

        coupleMoveUpdate(obj);
    }
}