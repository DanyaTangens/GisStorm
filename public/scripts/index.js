let map;

let couple_settings_change = 1;
let couple_settings_id = null;

document.addEventListener("DOMContentLoaded", () => {
    map = L.map('map').setView([56.8383, 60.6031], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([56.8383, 60.6031]).addTo(map)
        .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
        .openPopup();

    map.on('click', onMapClick);
});


function setTool(id) {
    switch (id){
        case 0:
            current_tool = 0;
            break;
        case 1:
            current_tool = 1;
            // document.getElementById("cursor").style.visibility = "visible";
            break;
        case 2:
            current_tool = 2;
            break;
        default:
            current_tool = 0;
            break;
    }
}
