<?php
// meteo.php
ob_start(); // Start output buffering

header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';

// Gestion du thème et de la langue
$theme = $_GET['theme'] ?? 'light';
$lang = $_GET['lang'] ?? 'fr';
$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';

// Validation du thème
$validThemes = ['light', 'dark'];
if (!in_array($theme, $validThemes)) {
    $theme = 'light';
    $cssFile = 'style-light.css';
}

// Image de fond selon le thème
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';

// Fonction pour sauvegarder une consultation
function saveCityConsultation($city) {
    $filename = 'city_logs.csv';
    $date = date('Y-m-d H:i:s');
    $ip = getClientIp();
    
    if (!file_exists($filename)) {
        $file = @fopen($filename, 'w');
        if ($file === false) {
            error_log("Erreur : Impossible de créer le fichier $filename");
            return;
        }
        fwrite($file, "\xEF\xBB\xBF"); // BOM pour UTF-8
        fputcsv($file, ['city', 'date', 'ip']);
        fclose($file);
    }
    
    $file = @fopen($filename, 'a');
    if ($file === false) {
        error_log("Erreur : Impossible d'ouvrir le fichier $filename en mode append");
        return;
    }
    fputcsv($file, [$city, $date, $ip]);
    fclose($file);
    
    setcookie('last_city', json_encode([
        'city' => $city,
        'date' => $date,
        'ip' => $ip
    ]), time() + (86400 * 30));
}

// Fonction pour charger un fichier CSV
function loadCSV($file) {
    $data = [];
    if (!file_exists($file)) return $data;
    if (($handle = fopen($file, 'r')) !== false) {
        $headers = fgetcsv($handle);
        if ($headers === false) return $data;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) continue;
            $data[] = array_combine($headers, $row);
        }
        fclose($handle);
    }
    return $data;
}

$regions = loadCSV('data/regions.csv');
$departements = loadCSV('data/departements.csv');
$villes = loadCSV('data/villes.csv');

$selected_region = $_GET['region'] ?? '';
$selected_departement = $_GET['departement'] ?? '';
$selected_ville = $_GET['ville'] ?? '';

// Enregistrer la ville dans le CSV si sélectionnée
if ($selected_ville) {
    $city = htmlspecialchars(strip_tags($selected_ville), ENT_QUOTES, 'UTF-8');
    if ($city) {
        saveCityConsultation($city);
    }
}

$filtered_departements = array_filter($departements, function($dep) use ($selected_region, $regions) {
    if ($selected_region === '') return true;
    foreach ($regions as $region) {
        if ($region['name'] === $selected_region && $dep['region_code'] === $region['code']) {
            return true;
        }
    }
    return false;
});

$filtered_villes = array_filter($villes, function($ville) use ($selected_departement) {
    return $selected_departement === '' || (isset($ville['department_code']) && $ville['department_code'] === $selected_departement);
});
// Récupérer les données météo via l'API
// Récupérer les données météo via l'API
$apiKey = '77e34d22a2f4e7e1b4caa003f1ef18fc'; // Vérifiez que votre clé API est valide
$weather_data = $selected_ville ? getWeatherByCity($selected_ville, $apiKey) : null;
$forecast_data = $selected_ville ? get5DayForecast($selected_ville, $apiKey) : null;

?>
    <style type="text/css">
        body {
            background: url('<?= htmlspecialchars($bgImage) ?>') no-repeat center center fixed;
            background-size: cover;
        }
        .block {
            background-color: rgba(255, 255, 255, 0.85); /* Light theme */
            padding: 30px;
            margin: 40px auto;
            max-width: 1100px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        body.dark .block {
            background-color: rgba(10, 10, 10, 0.8); /* Dark theme */
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.05);
        }
        .map-container {
            background-color: rgba(255, 255, 255, 0.85); /* Light theme */
            margin: 40px auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }
        body.dark .map-container {
            background-color: rgba(10, 10, 10, 0.8); /* Dark theme */
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.05);
        }
        .map-container figure {
            margin: 0;
            line-height: 0; /* Remove inline-block spacing */
        }
        .map-container img {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: block; /* Remove inline-block gaps */
            pointer-events: auto;
        }
        body.dark .map-container img {
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
        }
        .map-container figcaption {
            margin-top: 15px;
            font-size: 16px;
            color: #666; /* Light theme */
            font-style: italic;
            line-height: 1.4;
        }
        body.dark .map-container figcaption {
            color: #ccc; /* Dark theme */
        }
        area {
            outline: none;
        }
        area:hover {
            cursor: pointer;
        }
        .meteo-selection {
            background-color: rgba(255, 255, 255, 0.85); /* Light theme */
            padding: 30px;
            margin: 40px auto;
            max-width: 1100px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        body.dark .meteo-selection {
            background-color: rgba(10, 10, 10, 0.8); /* Dark theme */
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.05);
        }
        .meteo-selection form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            align-items: center;
        }
        .meteo-selection label {
            font-size: 18px;
            font-weight: 500;
            color: #1e3c72; /* Light theme */
            margin-right: 10px;
        }
        body.dark .meteo-selection label {
            color: #ffd700; /* Dark theme */
        }
        .meteo-selection select {
            padding    padding: 10px;
            font-size: 16px;
            border: 1px solid #1e3c72; /* Light theme */
            border-radius: 8px;
            background-color: #ffffff;
            color: #000000;
            min-width: 200px;
            transition: border-color 0.3s ease;
        }
        body.dark .meteo-selection select {
            border-color: #ffd700; /* Dark theme */
            background-color: #333333;
            color: #ffffff;
        }
        .meteo-selection select:focus {
            outline: none;
            border-color: #2a5298; /* Light theme hover */
        }
        body.dark .meteo-selection select:focus {
            border-color: #ffeb3b; /* Dark theme hover */
        }
        .meteo-selection select.updated {
            border-color: #28a745; /* Visual feedback when updated */
        }
        .meteo-selection button {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            background-color: #1e3c72; /* Light theme */
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        body.dark .meteo-selection button {
            background-color: #ffd700; /* Dark theme */
            color: #000000;
        }
        .meteo-selection button:hover {
            background-color: #2a5298; /* Light theme hover */
        }
        body.dark .meteo-selection button:hover {
            background-color: #ffeb3b; /* Dark theme hover */
        }
        .weather-forecast {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        .forecast-day {
            background-color: rgba(255, 255, 255, 0.9); /* Light theme */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 220px;
            transition: transform 0.2s ease;
        }
        body.dark .forecast-day {
            background-color: rgba(25, 25, 25, 0.95); /* Dark theme */
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
        }
        .forecast-day:hover {
            transform: scale(1.05);
        }
        .forecast-day h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #1e3c72; /* Light theme */
        }
        body.dark .forecast-day h3 {
            color: #ffd700; /* Dark theme */
        }
        .forecast-day p {
            font-size: 16px;
            margin: 5px 0;
            color: #333; /* Light theme */
        }
        body.dark .forecast-day p {
            color: #ccc; /* Dark theme */
        }
        .forecast-day img {
            width: 80px;
            height: 80px;
            margin-top: 10px;
        }
        .error-message {
            text-align: center;
            color: #d32f2f; /* Light theme */
            font-weight: 500;
            font-size: 16px;
            margin: 20px 0;
        }
        body.dark .error-message {
            color: #ef5350; /* Dark theme */
        }
    </style>
</head>
<body class="meteo-page">
<a id="top"></a>
<div class="wrapper">
    <?php require_once 'include/header.inc.php'; ?>

    <main>
        <!-- Bloc 1 : Carte -->
        <section class="block">
            <h2>🗺️ Sélectionnez une région</h2>
            <div class="map-container">
                <figure>
                    <img src="images/map.png" usemap="#image-map" alt="Carte des régions de France" />
                    <figcaption>🗺️ Cliquez sur une région pour voir les prévisions</figcaption>
                </figure>
                <map name="image-map">
                    <area target="" alt="Bretagne" title="Bretagne" href="meteo.php?region=Bretagne" coords="14,118,20,111,33,109,49,113,61,106,73,102,84,122,97,121,110,122,118,128,125,131,129,124,135,127,134,146,128,152,124,163,115,159,99,165,86,176,26,152,19,139,26,133" shape="poly">
                    <area target="" alt="Normandie" title="Normandie" href="meteo.php?region=Normandie" coords="115,68,133,72,137,87,168,93,190,86,177,82,218,61,223,74,219,104,213,109,208,121,197,125,188,144,181,143,177,134,162,137,159,126,142,131,122,114" shape="poly">
                    <area target="" alt="Grand-Est" title="Grand-Est" href="meteo.php?region=Grand-Est" coords="325,65,308,70,309,76,298,95,287,100,277,120,280,134,274,145,287,167,300,169,315,162,325,179,345,182,363,166,390,178,394,191,402,195,433,126,433,118,412,109,379,96,356,90,326,80" shape="poly">
                    <area target="" alt="Hauts-de-France" title="Hauts-de-France" href="meteo.php?region=Hauts-de-France" coords="257,18,231,21,218,48,219,62,225,76,220,106,237,107,246,104,248,111,269,111,278,122,286,108,288,98,299,94,310,74,305,55,288,44,273,30" shape="poly">
                    <area target="" alt="Ile-de-France" title="Ile-de-France" href="meteo.php?region=Ile-de-France" coords="222,105,212,110,209,121,212,132,227,150,241,150,244,156,257,157,263,148,275,146,279,135,276,119,268,109,249,109,245,102,238,106" shape="poly">
                    <area target="" alt="Pays-de-la-Loire" title="Pays-de-la-Loire" href="meteo.php?region=Pays-de-la-Loire" coords="139,127,143,131,156,127,163,137,176,134,180,143,198,151,192,162,185,167,185,173,168,175,170,189,163,192,165,197,142,198,139,203,122,198,139,217,138,234,122,235,133,239,114,232,103,224,94,201,97,189,89,185,84,175,100,163,116,161,125,162,130,153,136,153,134,145" shape="poly">
                    <area target="" alt="Centre-Val-de-Loire" title="Centre-Val-de-Loire" href="meteo.php?region=Centre-Val-de-Loire" coords="211,124,197,125,197,141,190,145,197,154,193,163,185,167,185,175,170,177,170,188,164,191,165,198,171,206,179,206,189,217,197,237,232,239,241,233,241,226,262,222,256,211,257,197,254,185,257,179,260,168,259,157,245,155,227,148,211,130" shape="poly">
                    <area target="" alt="Bourgogne-Franche-Comté" title="Bourgogne-Franche-Comté" href="meteo.php?region=Bourgogne-Franche-Comté" coords="276,146,265,147,258,155,261,168,257,178,254,186,256,208,262,220,255,226,270,228,278,224,285,235,291,237,289,249,303,255,309,249,318,253,322,239,336,239,333,245,339,250,351,252,363,243,397,199,387,178,364,165,346,180,324,179,316,161,300,169,285,165" shape="poly">
                    <area target="" alt="Nouvelle-Aquitaine" title="Nouvelle-Aquitaine" href="meteo.php?region=Nouvelle-Aquitaine" coords="123,198,138,203,141,198,164,196,170,206,179,207,197,238,231,238,242,255,242,263,235,269,240,278,222,311,195,310,188,324,190,331,179,335,182,341,178,349,166,355,149,354,135,361,134,373,129,376,140,376,143,387,129,409,74,379,113,280,128,304,113,268,99,257,114,261,121,252,118,242,134,238,140,235,140,216" shape="poly">
                    <area target="" alt="Auvergne-Rhônes-Alpes" title="Auvergne-Rhônes-Alpes" href="meteo.php?region=Auvergne-Rhônes-Alpes" coords="256,223,242,226,243,234,233,237,242,252,243,262,236,267,241,280,223,309,218,318,223,320,223,326,234,329,245,315,253,328,260,316,270,319,270,325,276,320,283,325,290,345,298,346,327,353,339,357,347,353,339,343,347,339,345,331,350,332,360,325,371,325,368,318,362,311,390,307,397,301,395,269,392,251,387,246,374,247,362,260,365,250,364,242,351,250,339,249,332,244,335,237,323,238,318,250,308,248,303,254,289,248,290,236,284,234,279,222,271,226" shape="poly">
                    <area target="" alt="Occitanie" title="Occitanie" href="meteo.php?region=Occitanie" coords="131,409,163,420,208,437,236,440,252,435,294,387,310,377,314,365,312,350,291,345,276,319,272,325,269,317,260,316,253,327,244,313,235,327,224,325,219,315,214,310,196,310,191,328,180,332,182,338,168,354,149,350,136,359,135,369,130,373,140,374,144,387" shape="poly">
                    <area target="" alt="Provence-Alpes-Côte-d'Azur" title="Provence-Alpes-Côte-d'Azur" href="meteo.php?region=Provence-Alpes-Côte-d'Azur" coords="381,307,362,312,370,320,372,326,360,324,351,332,345,331,348,338,340,342,348,353,340,360,334,353,328,355,328,349,316,348,315,361,310,376,298,386,313,393,350,409,379,405,414,379,421,363,418,357,390,349,392,343,396,334,394,326" shape="poly">
                    <area target="" alt="Corse" title="Corse" href="meteo.php?region=Corse" coords="457,401,451,401,449,415,429,420,416,441,428,474,429,481,440,487,449,488,460,448,460,426" shape="poly">
                </map>
            </div>
        </section>

        <!-- Bloc 2 : Sélection -->
        <section class="block meteo-selection">
            <h2>📍 Choisissez une région, un département et une ville</h2>
            <form method="get" action="meteo.php">
                <label for="region">Région :</label>
                <select name="region" id="region" onchange="this.form.submit()">
                    <option value="">-- Choisir une région --</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= htmlspecialchars($region['name']) ?>" <?= $selected_region === $region['name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($region['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="departement">Département :</label>
                <select name="departement" id="departement" onchange="this.form.submit()">
                    <option value="">-- Choisir un département --</option>
                    <?php foreach ($filtered_departements as $dep): ?>
                        <option value="<?= htmlspecialchars($dep['code']) ?>" <?= $selected_departement === $dep['code'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dep['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="ville">Ville :</label>
                <select name="ville" id="ville" onchange="this.form.submit()">
                    <option value="">-- Choisir une ville --</option>
                    <?php foreach ($filtered_villes as $ville): ?>
                        <option value="<?= htmlspecialchars($ville['name']) ?>" <?= $selected_ville === $ville['name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ville['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="hidden" name="theme" value="<?= htmlspecialchars($theme) ?>">
                <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
                <button type="submit">Rechercher</button>
            </form>
        </section>

        <!-- Bloc 3 : Prévisions -->
        <section class="block">
    <?php if ($selected_ville): ?>
        <?php if ($weather_data): ?>
            <h2>🌤️ Météo actuelle pour <?= htmlspecialchars($selected_ville) ?></h2>
            <p>Température : <?= $weather_data['temp'] ?>°C</p>
            <p>Ressenti : <?= $weather_data['feels_like'] ?>°C</p>
            <p>Humidité : <?= $weather_data['humidity'] ?>%</p>
            <p>Vent : <?= $weather_data['wind'] ?> km/h</p>
            <p>Lever du soleil : <?= $weather_data['sunrise'] ?></p>
            <p>Coucher du soleil : <?= $weather_data['sunset'] ?></p>
            <p>Conditions : <?= $weather_data['desc'] ?></p>
            <img src="https://openweathermap.org/img/wn/<?= $weather_data['icon'] ?>@2x.png" alt="Icône météo" />
        <?php else: ?>
            <p class="error-message">Erreur : Impossible de récupérer la météo pour <?= htmlspecialchars($selected_ville) ?>. Vérifiez le nom de la ville ou la clé API.</p>
        <?php endif; ?>

        <!-- Prévisions sur 5 jours -->
        <?php if ($forecast_data && is_array($forecast_data)): ?>
            <h2>Prévisions pour les 5 prochains jours :</h2>
            <div class="weather-forecast">
                <?php foreach ($forecast_data as $forecast): ?>
                    <?php if (isset($forecast['temp']['day'], $forecast['weather'][0]['description'], $forecast['weather'][0]['icon'])): ?>
                        <div class="forecast-day">
                            <h3><?= date('d/m/Y', $forecast['dt']) ?></h3>
                            <p>Température : <?= $forecast['temp']['day'] ?>°C</p>
                            <p>Conditions : <?= ucfirst($forecast['weather'][0]['description']) ?></p>
                            <p>Humidité : <?= $forecast['humidity'] ?>%</p>
                            <p>Vent : <?= $forecast['wind_speed'] ?> km/h</p>
                            <img src="https://openweathermap.org/img/wn/<?= $forecast['weather'][0]['icon'] ?>@2x.png" alt="Icône météo" />
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="error-message">Erreur : Aucune prévision disponible pour <?= htmlspecialchars($selected_ville) ?>. Vérifiez la ville ou la clé API.</p>
        <?php endif; ?>
    <?php endif; ?>
</section>
        </main>

<?php require_once 'include/footer.inc.php'; ?>

    <a href="#top" class="back-to-top" title="Retour en haut">⬆️</a>
</div>

<script>
    document.querySelectorAll('map area').forEach(area => {
        area.addEventListener('click', (e) => {
            e.preventDefault(); // Empêche la redirection par défaut
            const region = area.getAttribute('title');
            const regionSelect = document.getElementById('region');

            // Met à jour le <select> de région
            for (let i = 0; i < regionSelect.options.length; i++) {
                if (regionSelect.options[i].value === region) {
                    regionSelect.selectedIndex = i;
                    regionSelect.classList.add('updated');
                    break;
                }
            }

            // Soumet le formulaire pour recharger les départements et villes
            regionSelect.form.submit();
        });
    });
</script>
</body>
</html>
<?php
ob_end_flush(); // End output buffering
?>