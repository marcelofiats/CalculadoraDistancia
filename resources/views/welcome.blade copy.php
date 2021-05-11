<!DOCTYPE html>
<html>

<head>
    <title>Calculadora de Distância</title>
    <style>
        #map {
            margin-top: 15px;
            height: 900px;
            width: 100%;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header text-center">
                    <h3>Calculadora de Distância</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="origin">Origem: </label>
                            <input class="form-control" type="text" id="origin" name="origin">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label for="destiny">Destino: </label>
                            <input class="form-control" type="text" id="destiny" name="destiny">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button id="save" onclick="loadDistance()" class="btn btn-success"> Calcular </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="distance">Distância</label>
                            <input class="form-control" type="text" name="distance" id="distance">
                        </div>
                        <div class="col-md-12">
                            <label for="price">Preço</label>
                            <input class="form-control" type="text" name="price" id="price">
                        </div>
                    </div>
                </div>
                <div id="map"></div>
            </div>
        </div>

        <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBp7F8FaDQTAqcwLwn1BhGBki4muVhgD_w&callback=initMap&libraries=places&v=weekly" async></script>
        <script src="{{ asset('js/app.js') }}"></script>
        <script>
            $(function() {
                initMap();
            });

            function initMap() {
                const map = new google.maps.Map(document.getElementById("map"), {
                    mapTypeControl: false,
                    center: {
                        lat: -22.73808148889322,
                        lng: -47.33421687687745
                    },
                    zoom: 15,
                });
                new AutocompleteDirectionsHandler(map);
            }

            class AutocompleteDirectionsHandler {
                constructor(map) {
                    this.map = map;
                    this.originPlaceId = "";
                    this.destinationPlaceId = "";
                    this.travelMode = google.maps.TravelMode.DRIVING;
                    this.directionsService = new google.maps.DirectionsService();
                    this.directionsRenderer = new google.maps.DirectionsRenderer();
                    this.directionsRenderer.setMap(map);

                    const originInput = document.getElementById("origin");
                    const destinationInput = document.getElementById("destiny");

                    const originAutocomplete = new google.maps.places.Autocomplete(originInput);
                    // Specify just the place data fields that you need.
                    originAutocomplete.setFields(["place_id"]);
                    const destinationAutocomplete = new google.maps.places.Autocomplete(destinationInput);
                    // Specify just the place data fields that you need.
                    destinationAutocomplete.setFields(["place_id"]);

                    this.setupPlaceChangedListener(originAutocomplete, "ORIG");
                    this.setupPlaceChangedListener(destinationAutocomplete, "DEST");
                }
                setupPlaceChangedListener(autocomplete, mode) {
                    autocomplete.bindTo("bounds", this.map);
                    autocomplete.addListener("place_changed", () => {
                        const place = autocomplete.getPlace();
                        if (mode === "ORIG") {
                            this.originPlaceId = place.place_id;
                            $('#origins_id').val(place.place_id);
                            var origin = $('#origin').val();
                            var destination = $('#destiny').val();
                            var service = new google.maps.DistanceMatrixService();
                            service.getDistanceMatrix(
                            {
                                origins: [origin],
                                destinations: [destination],
                                travelMode: google.maps.TravelMode.DRIVING,
                                unitSystem: google.maps.UnitSystem.metric,
                                avoidHighways: false,
                                avoidTolls: false
                            }, callback);
                        } else {
                            this.destinationPlaceId = place.place_id;
                            $('#destinations_id').val(place.place_id);
                        }
                    });
                }
            }
            function callback(response, status) {
                if (status ==='OK') {
                var distanceText = response.rows[0].elements[0].distance.text;
                var distance = (response.rows[0].elements[0].distance.value);
                $('#distance').val(distanceText);
                if (distance < 20000) {
                    var valor = (distance/ 1000) * 2.5;
                    var price = valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    $('#price').val(price);
                } else {
                    alert('este estabelecimento não faz entregas acima de 20km');
                }

                } else {
                alert('Ocorreu um erro na execução');
                }
            }



        </script>
</body>

</html>
