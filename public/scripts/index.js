let map;

let couple_settings_change = 0;
let couple_settings_id = null;

document.addEventListener("DOMContentLoaded", () => {
    map = L.map('map').setView([56.8383, 60.6031], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    map.on('click', onMapClick);
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
function pointToLayer(feature, latlng) {
    return L.marker(latlng, {icon: leafletIcon(feature)});
}

function leafletIcon(feature) {
    if (feature.properties.obj == 3) {
        return L.icon({
            iconUrl: 'images/coupling.png',
            iconAnchor: [15, 30],
            popupAnchor: [0, 0],
            tooltipAnchor: [0, 0]
        });
    }
}

function onEachFeature(feature, layer) {

    var node_type = '';
    if (feature.properties.type_coupling === 0) {
        node_type = "Прямая муфта";
    } else if (feature.properties.type_coupling === 1) {
        node_type = "Разветвительная муфта";
    }

    // couple_list.push(parseInt(feature.properties.id));

    id = feature.properties.id; //айдишник
    name = feature.properties.name; // наименование копируемого
    type_coupling = feature.properties.type_coupling; //тип копируемого
    description = feature.properties.description; // описание
    lonLS = feature.geometry.coordinates[0]; // какая-то координата
    latLS = feature.geometry.coordinates[1]; // какая-то координа, но вторая

    layer.options.draggable = true;
    layer.on('dragend', function (event) {
        // saveNewData(event);
    });
    layer.bindPopup(
        `<b>Информация о муфте:</b><br>
            ID: ${id}<br>
            Наименование муфты: ${name == null ? 'нет' : name}<br> 
            Тип: ${node_type}<br>
            Доп. инфо: ${description == null ? 'нет' : description}<br> 
            Текущие координаты: ${latLS + ',' + lonLS}<br>
            <input type=submit id=couple_settings onclick='couple_settings(${lsID});' value='Изменить параметры'>&nbsp;
            <input type=submit id=couple_delete onclick='couple_delete(${lsID});' value='Удалить'>&nbsp;<br>
            <input type=submit id=couple_files onclick='couple_files(${lsID}, ${feature.properties.sline_id});' value='Управление файлами'>&nbsp;
            <input type=submit id=couple_copy onclick='couple_copy(\"${lsName}\",\"${lsNomination}\",${lsType},\"${lsComment}\",${latLS},${lonLS});' value='Копировать'><br>
            `
    );
}