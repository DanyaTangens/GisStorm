let map;

let couple_settings_change = 0;
let couple_settings_id = null;
let current_tool = 0;

document.addEventListener("DOMContentLoaded", () => {
    map = L.map('map').setView([56.8383, 60.6031], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    map.on('click', onMapClick);

    get_coupling()
});


function setTool(id) {
    switch (id) {
        case 0:
            current_tool = 0;
            break;
        case 1:
            current_tool = 1;
            break;
        case 2:
            current_tool = 2;
            couple_settings_change = 0;
            break;
        default:
            current_tool = 0;
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

    let node_type = '';
    if (feature.properties.type_coupling === 0) {
        node_type = "Прямая муфта";
    } else if (feature.properties.type_coupling === 1) {
        node_type = "Разветвительная муфта";
    }

    couple_list.push(parseInt(feature.properties.id));

    let id = feature.properties.id; //айдишник
    let name = feature.properties.name; // наименование копируемого
    let type_coupling = feature.properties.type_coupling; //тип копируемого
    let description = feature.properties.description; // описание
    let lonLS = feature.geometry.coordinates[0]; // какая-то координата
    let latLS = feature.geometry.coordinates[1]; // какая-то координа, но вторая

    layer.options.draggable = true;
    layer.on('dragend', function (event) {
        saveNewData(event);
    });
    layer.bindPopup(
        `<b>Информация о муфте:</b><br>
            ID: ${id}<br>
            Наименование муфты: ${name == null ? 'нет' : name}<br> 
            Тип: ${node_type}<br>
            Доп. инфо: ${description == null ? 'нет' : description}<br> 
            Текущие координаты: ${latLS + ',' + lonLS}<br>
            <input type=submit id=couple_settings onclick='couple_settings(${id});' value='Изменить параметры'>&nbsp;
            <input type=submit id=couple_delete onclick='couple_delete(${id});' value='Удалить'>&nbsp;<br>
            `
    );
}

function saveNewData(e) {
    console.log(e)
    if (e.target.feature.properties.obj === 2) {
        let obj = { 'id': 0, 'action': 'put', 'slid': 0, 'type': 0, 'lat': 0, 'lng': 0, 'obj': 2 };
        obj.id = parseInt(e.target.feature.properties.id);
        obj.type =e.target.feature.properties.type;
        obj.obj = e.target.feature.properties.obj;
        obj.lat = e.target._latlng.lat;
        obj.lng = e.target._latlng.lng;

        saveDataCoupling(obj);
    }
}