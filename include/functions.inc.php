<?php

// Fonction utilitaire pour gérer le cache
function getCachedData($cacheKey, $ttl = 3600) {
    // Chemin relatif à la racine du projet (remonter d'un dossier depuis include/)
    $cacheDir = __DIR__ . '/../cache/';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . md5($cacheKey) . '.cache';
    
    // Vérifier si le cache existe et n'est pas expiré
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    return null;
}

function setCachedData($cacheKey, $data) {
    // Chemin relatif à la racine du projet
    $cacheDir = __DIR__ . '/../cache/';
    $cacheFile = $cacheDir . md5($cacheKey) . '.cache';
    file_put_contents($cacheFile, json_encode($data));
}

// Cette fonction retourne l’adresse IP réelle du visiteur
function getClientIp() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'; 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return trim($ip);
}

// Cette fonction interroge l’API NASA APOD pour obtenir l’image du jour avec cache
function getApodData($apiKey) {
    $date = date('Y-m-d'); 
    $cacheKey = "apod_$date";
    
    // Vérifier le cache
    $cachedData = getCachedData($cacheKey, 86400); // Cache de 24h
    if ($cachedData !== null) {
        return $cachedData;
    }

    $url = "https://api.nasa.gov/planetary/apod?api_key=$apiKey&date=$date";
    $response = @file_get_contents($url); 
    
    if ($response === false) {
        $data = [
            'media_type' => 'error',
            'url' => '',
            'explanation' => 'Erreur : Impossible de contacter l\'API NASA.'
        ];
    } else {
        $data = json_decode($response, true); 
        if (!$data || !is_array($data)) {
            $data = [
                'media_type' => 'error',
                'url' => '',
                'explanation' => 'Erreur : Réponse JSON invalide.'
            ];
        } else {
            $data = [
                'media_type' => $data['media_type'] ?? 'error',
                'url' => $data['url'] ?? '',
                'explanation' => $data['explanation'] ?? 'Aucune description disponible.'
            ];
        }
    }

    // Stocker dans le cache
    setCachedData($cacheKey, $data);
    return $data;
}
function getApodDataCurl($apiKey) {
    $date = date('Y-m-d');
    $cacheKey = "apod_$date";

    $cachedData = getCachedData($cacheKey, 86400);
    if ($cachedData !== null) return $cachedData;

    $url = "https://api.nasa.gov/planetary/apod?api_key=$apiKey&date=$date";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $data = [
            'media_type' => 'error',
            'url' => '',
            'explanation' => 'Erreur : Impossible de contacter l\'API NASA.'
        ];
    } else {
        $data = json_decode($response, true);
        if (!is_array($data)) {
            $data = [
                'media_type' => 'error',
                'url' => '',
                'explanation' => 'Erreur : Réponse JSON invalide.'
            ];
        }
    }

    curl_close($ch);
    setCachedData($cacheKey, $data);
    return $data;
}

// Cette fonction utilise GeoPlugin pour localiser l’utilisateur via son IP avec cache
function getGeoPluginData($ip) {
    $cacheKey = "geoplugin_$ip";
    
    // Vérifier le cache
    $cachedData = getCachedData($cacheKey, 86400); // Cache de 24h
    if ($cachedData !== null) {
        return $cachedData;
    }

    $url = "http://www.geoplugin.net/xml.gp?ip=$ip";
    $xml = @simplexml_load_file($url);
    if ($xml === false) {
        $data = [
            'country' => 'Inconnu',
            'region' => 'Inconnu',
            'city' => 'Inconnu'
        ];
    } else {
        $data = [
            'country' => (string)($xml->geoplugin_countryName ?? 'Inconnu'),
            'region' => (string)($xml->geoplugin_region ?? 'Inconnu'),
            'city' => (string)($xml->geoplugin_city ?? 'Inconnu')
        ];
    }

    // Stocker dans le cache
    setCachedData($cacheKey, $data);
    return $data;
}

// Retourne les infos géographiques de l’IP avec clé API via ipinfo.io avec cache
function getIpInfoData($ip, $token) {
    $cacheKey = "ipinfo_$ip";
    
    // Vérifier le cache
    $cachedData = getCachedData($cacheKey, 86400); // Cache de 24h
    if ($cachedData !== null) {
        return $cachedData;
    }

    $url = "https://ipinfo.io/$ip/geo?token=$token";
    $response = @file_get_contents($url);
    if ($response === false) {
        $data = [
            'ip' => $ip,
            'city' => 'Inconnu',
            'region' => 'Inconnu',
            'country' => 'Inconnu',
            'loc' => 'Inconnu'
        ];
    } else {
        $data = json_decode($response, true);
        if (!$data || !is_array($data)) {
            $data = [
                'ip' => $ip,
                'city' => 'Inconnu',
                'region' => 'Inconnu',
                'country' => 'Inconnu',
                'loc' => 'Inconnu'
            ];
        } else {
            $data = [
                'ip' => $data['ip'] ?? $ip,
                'city' => $data['city'] ?? 'Inconnu',
                'region' => $data['region'] ?? 'Inconnu',
                'country' => $data['country'] ?? 'Inconnu',
                'loc' => $data['loc'] ?? 'Inconnu'
            ];
        }
    }

    // Stocker dans le cache
    setCachedData($cacheKey, $data);
    return $data;
}

// Cette fonction interroge l'API whatismyip.com avec cache
function getWhatIsMyIpData($ip, $apiKey) {
    $cacheKey = "whatismyip_$ip";
    
    // Vérifier le cache
    $cachedData = getCachedData($cacheKey, 86400); // Cache de 24h
    if ($cachedData !== null) {
        return $cachedData;
    }

    $url = "https://api.whatismyip.com/ip-address-lookup.php?key=$apiKey&input=$ip";
    $response = @file_get_contents($url);
    if ($response === false || empty(trim($response))) {
        $data = [
            'ip' => $ip,
            'city' => 'Inconnu (Erreur réseau)',
            'region' => 'Inconnu',
            'country' => 'Inconnu',
            'latitude' => 'Inconnu',
            'longitude' => 'Inconnu'
        ];
    } else {
        $lines = explode("\n", trim($response));
        $data = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, ':') === false) continue;
            list($label, $value) = explode(':', $line, 2);
            $data[trim($label)] = trim($value);
        }
        $data = [
            'ip' => $data['IP Address'] ?? $ip,
            'city' => $data['City'] ?? 'Inconnu',
            'region' => $data['Region'] ?? 'Inconnu',
            'country' => $data['Country'] ?? 'Inconnu',
            'latitude' => $data['Latitude'] ?? 'Inconnu',
            'longitude' => $data['Longitude'] ?? 'Inconnu'
        ];
    }

    // Stocker dans le cache
    setCachedData($cacheKey, $data);
    return $data;
}

// Cette fonction gère un compteur de visites simple
function getPageHits() {
    $counterFile = __DIR__ . '/../counter.txt'; // Ajustement du chemin
    if (!file_exists($counterFile)) {
        file_put_contents($counterFile, '0');
    }
    $hits = (int)file_get_contents($counterFile);
    $hits++;
    file_put_contents($counterFile, $hits);
    return $hits;
}

// Cette fonction retourne une image aléatoire depuis un dossier donné
function getRandomBackgroundImage($dir = 'images/randomimage/') {
    $dir = __DIR__ . '/../' . $dir; // Ajustement du chemin
    $default = $dir . 'default.jpg';
    if (!is_dir($dir)) return $default;

    $images = array_filter(scandir($dir), function($file) use ($dir) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png']) && is_file($dir . $file);
    });

    $images = array_values($images);
    return !empty($images) ? $dir . $images[array_rand($images)] : $default;
}

function getRandomInspirationImage($dir = 'images/randomimages/') {
    $dir = __DIR__ . '/../' . $dir; // Ajustement du chemin
    $default = $dir . 'default.jpg';
    if (!is_dir($dir)) return $default;

    $images = array_filter(scandir($dir), function($file) use ($dir) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png']) && is_file($dir . $file);
    });

    $images = array_values($images);
    return !empty($images) ? $dir . $images[array_rand($images)] : $default;
}

// Fonction météo avec cache
function getWeatherByCity($city, $apiKey) {
    $cityEncoded = urlencode($city);
    $cacheKey = "weather_$cityEncoded";
    
    // Vérifier le cache
    $cachedData = getCachedData($cacheKey, 1800); // Cache de 30 minutes
    if ($cachedData !== null) {
        return $cachedData;
    }

    $url = "https://api.openweathermap.org/data/2.5/weather?q=$cityEncoded&units=metric&lang=fr&appid=$apiKey";
    $response = @file_get_contents($url);
    if ($response === false) {
        $data = null;
    } else {
        $data = json_decode($response, true);
        if (!isset($data['main'])) {
            $data = null;
        } else {
            $data = [
                'temp' => round($data['main']['temp']),
                'feels_like' => round($data['main']['feels_like']),
                'humidity' => $data['main']['humidity'],
                'wind' => round($data['wind']['speed'] * 3.6), // m/s → km/h
                'sunrise' => date('H:i', $data['sys']['sunrise']),
                'sunset' => date('H:i', $data['sys']['sunset']),
                'desc' => ucfirst($data['weather'][0]['description']),
                'icon' => $data['weather'][0]['icon'],
                'date' => date('d/m/Y H:i'),
            ];
            
        }
    }

    // Stocker dans le cache
    setCachedData($cacheKey, $data);
    return $data;
}


function getWeatherNewsFromGNews($city, $apiKey) {
    $query = urlencode("météo $city");
    $url = "https://gnews.io/api/v4/search?q=$query&lang=fr&max=1&apikey=$apiKey";

    $json = @file_get_contents($url);
    if (!$json) return null;

    $data = json_decode($json, true);
    if (!isset($data['articles'][0])) return null;

    $article = $data['articles'][0];
    return [
        'title' => $article['title'],
        'description' => $article['description'],
        'url' => $article['url'],
        'image' => $article['image'] ?? 'images/icons/news.png'
    ];
}

function getMultipleWeatherNews($query, $apiKey, $max = 6) {
    $queryEncoded = urlencode($query);
    $cacheKey = "gnews_" . md5($queryEncoded); // clé unique
    $cached = getCachedData($cacheKey, 3600); // cache de 1h
    if ($cached !== null) return $cached;

    $url = "https://gnews.io/api/v4/search?q=$queryEncoded&lang=fr&max=$max&apikey=$apiKey";
    $json = @file_get_contents($url);
    if (!$json) return [];

    $data = json_decode($json, true);
    if (!isset($data['articles']) || !is_array($data['articles'])) return [];

    setCachedData($cacheKey, $data['articles']); // on stocke les résultats
    return $data['articles'];
}


// Essaie d’abord whatismyip.com, sinon ip-api.com
function getGeoDataWithFallback($ip, $whatismyipKey) {
    $whats = getWhatIsMyIpData($ip, $whatismyipKey);

    $isValid = isset($whats['city']) && $whats['city'] !== 'Inconnu' && $whats['city'] !== 'Inconnu (Erreur réseau)';
    if ($isValid) {
        $whats['source'] = 'WhatIsMyIP';
        return $whats;
    }

    // Sinon, fallback vers ip-api.com
    $url = "http://ip-api.com/json/$ip?fields=status,message,country,regionName,city,lat,lon,query";
    $response = @file_get_contents($url);
    $data = json_decode($response, true);

    if (!isset($data['status']) || $data['status'] !== 'success') {
        return [
            'ip' => $ip,
            'city' => 'Inconnu',
            'region' => 'Inconnu',
            'country' => 'Inconnu',
            'latitude' => 'Inconnu',
            'longitude' => 'Inconnu',
            'source' => 'Aucune API'
        ];
    }

    return [
        'ip' => $data['query'],
        'city' => $data['city'],
        'region' => $data['regionName'],
        'country' => $data['country'],
        'latitude' => $data['lat'],
        'longitude' => $data['lon'],
        'source' => 'ip-api.com'
    ];
}
// Fonction pour obtenir les coordonnées (latitude et longitude) de la ville via son nom
function getCoordinatesByCity($city, $apiKey) {
    $url = "http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey";
    $response = @file_get_contents($url);

    if ($response === false) {
        return null;
    } else {
        $data = json_decode($response, true);
        return [
            'lat' => $data['coord']['lat'],
            'lon' => $data['coord']['lon']
        ];
    }
}
// Fonction pour récupérer les prévisions sur 7 jours avec l'API OneCall
function get7DayForecast($lat, $lon, $apiKey) {
    $url = "https://api.openweathermap.org/data/2.5/onecall?lat=$lat&lon=$lon&exclude=current,minutely,hourly,alerts&units=metric&lang=fr&appid=$apiKey";
    $response = @file_get_contents($url);

    if ($response === false) {
        return null;
    } else {
        $data = json_decode($response, true);
        return $data['daily'] ?? null; // Retourne les prévisions sur 7 jours
    }
}
function get5DayForecast($city, $apiKey) {
    $cityEncoded = urlencode($city);
    $cacheKey = "forecast_5day_$cityEncoded";
    
    // Vérifier le cache
    $cachedData = getCachedData($cacheKey, 1800); // Cache de 30 minutes
    if ($cachedData !== null) {
        return $cachedData;
    }

    $url = "https://api.openweathermap.org/data/2.5/forecast?q=$cityEncoded&units=metric&lang=fr&appid=$apiKey";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        error_log("Erreur API OpenWeatherMap (get5DayForecast): HTTP $httpCode pour la ville $city");
        return null;
    }

    $data = json_decode($response, true);
    if (!isset($data['list'])) {
        error_log("Erreur API OpenWeatherMap: Prévisions non trouvées pour la ville $city");
        return null;
    }

    // Regrouper les prévisions par jour
    $dailyForecasts = [];
    $currentDay = '';
    foreach ($data['list'] as $entry) {
        $date = date('Y-m-d', $entry['dt']);
        $hour = date('H', $entry['dt']);
        
        // Prendre la prévision autour de midi (12h) pour représenter la journée
        if ($date !== $currentDay && $hour >= 9 && $hour <= 15) {
            $dailyForecasts[] = [
                'dt' => $entry['dt'],
                'temp' => ['day' => round($entry['main']['temp'])],
                'weather' => $entry['weather'],
                'humidity' => $entry['main']['humidity'],
                'wind_speed' => round($entry['wind']['speed'] * 3.6) // Convertir m/s en km/h
            ];
            $currentDay = $date;
        }
    }

    // Limiter à 5 jours maximum
    $dailyForecasts = array_slice($dailyForecasts, 0, 5);

    // Stocker dans le cache
    setCachedData($cacheKey, $dailyForecasts);
    return $dailyForecasts;
}

/**
 * Récupère les prévisions météo depuis weatherapi.com.
 *
 * @param string $city Ville.
 * @return array|null Prévisions météo.
 */
function getWeatherData($city) {
    $apiKey = "77e34d22a2f4e7e1b4caa003f1ef18fc"; 
    $url = "https://api.weatherapi.com/v1/forecast.json?key=$apiKey&q=$city&days=5";
    $response = file_get_contents($url);
    return json_decode($response, true); // Décodage de la réponse JSON
}

/**
 * Statistiques : nombre de consultations par ville.
 *
 * @return array Statistiques triées.
 */
function getCityStats() {
    $stats = [];
    $filename = 'city_logs.csv';

    if (file_exists($filename)) {
        if (($file = fopen($filename, 'r')) !== false) {
            fgetcsv($file); // Ignore header
            while (($data = fgetcsv($file)) !== false) {
                $city = $data[0];
                $stats[$city] = ($stats[$city] ?? 0) + 1;
            }
            fclose($file);
        }
    }
    arsort($stats);
    return $stats;
}

/**
 * Statistiques : nombre de visites par jour (5 derniers jours).
 *
 * @return array Visites par date.
 */
function getDailyVisits() {
    $daily = [];
    $filename = 'city_logs.csv';
    $today = new DateTime();
    $start = (clone $today)->modify('-4 days');

    for ($i = 0; $i < 5; $i++) {
        $date = (clone $start)->modify("+$i days")->format('Y-m-d');
        $daily[$date] = 0;
    }

    if (file_exists($filename)) {
        if (($file = fopen($filename, 'r')) !== false) {
            fgetcsv($file); // Ignore header
            while (($data = fgetcsv($file)) !== false) {
                if (isset($data[1])) {
                    $date = explode(' ', $data[1])[0];
                    if (isset($daily[$date])) {
                        $daily[$date]++;
                    }
                }
            }
            fclose($file);
        }
    }
    return $daily;
}
?>