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
    if (current_tool === 1) {
        marker = new L.Marker([e.latlng.lat, e.latlng.lng], {icon: getIcon(1)}).addTo(map); //было map
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

function getCoupling() {
    fetch('./test',
        {
            method: 'post',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title: 'Beispielprojekt',
                url: 'http://www.example.com',
            })
        })
        .then(function (response) {
            var text = response.json();
            console.log(text.title);
        })
        .catch(function (error) {
            console.error(error);
        });
}

function updateDataCoupling() {
    let obj = {'id': 0, 'action': '', 'name': '', 'type': 0, 'lat': 0, 'lon': 0, 'obj': 2};

    if (document.getElementById('coupling_nomination').value.trim() === '') {
        alert('Название не может быть пустым');
        return;
    }

    if (couple_settings_change === 0) { // insert
        obj.action = 'POST';
        obj.id = 0;
        obj.lat = current_coupling._latlng.lat;
        obj.lon = current_coupling._latlng.lng;
    } else {
        obj.action = 'PUT';
        obj.id = couple_settings_id;
        couple_settings_change = 0;
    }

    obj.name = $('#coupling_nomination').val();
    obj.type = $('#coupling_type').val();
    obj.comment = $('#coupling_sl_txt').val();
    obj.link = $('#coupling_folder').val();

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