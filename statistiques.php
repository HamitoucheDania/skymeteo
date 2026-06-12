<?php
/**
 * Fichier : statistiques.php
 * 
 * @file
 * @brief Page de statistiques du site SkyMétéo, affichant les consultations par ville et les visites quotidiennes sous forme de graphiques.
 * @author Imane MOUSSAOUI
 * @version 2.9
 * @package SkyMeteo
 */

// Activer l'affichage des erreurs pour le débogage (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Empêcher toute sortie prématurée
ob_start();

header('Content-Type: text/html; charset=UTF-8');
require_once 'include/functions.inc.php';

// Inclure header.inc.php au début, avant tout affichage HTML
require_once 'include/header.inc.php';

/**
 * @var string Thème de l'interface utilisateur ('light' ou 'dark').
 */
$theme = $_GET['theme'] ?? 'light';

/**
 * @var string Langue de l'interface ('fr' par défaut).
 */
$lang = $_GET['lang'] ?? 'fr';

/**
 * @var string Nom du fichier CSS à utiliser en fonction du thème.
 */
$cssFile = ($theme === 'dark') ? 'style-dark.css' : 'style-light.css';

/**
 * @var string Chemin de l'image de fond en fonction du thème.
 */
$bgImage = ($theme === 'dark') ? 'images/bg-dark.jpg' : 'images/bg-light.jpg';

/**
 * @var array Statistiques des consultations par ville.
 */
$cityStats = getCityStats();

/**
 * @var array Statistiques des visites par jour sur les 5 derniers jours.
 */
$dailyVisits = getDailyVisits();

/**
 * @var array|null Données de la dernière ville consultée (stockée dans un cookie) ou null si indisponible.
 */
$lastCity = isset($_COOKIE['last_city']) ? json_decode($_COOKIE['last_city'], true) : null;
if (!is_array($lastCity) || !isset($lastCity['city'], $lastCity['date'], $lastCity['ip'])) {
    $lastCity = null;
}

/**
 * @var int Nombre maximum de consultations pour normaliser les largeurs des barres (villes).
 */
$maxConsultations = (!isset($cityStats['error']) && !empty($cityStats)) ? max($cityStats) : 0;

/**
 * @var int Largeur maximale pour les barres des graphiques de villes (en pixels).
 */
$maxBarWidth = 600;

/**
 * @var int Nombre maximum de visites quotidiennes pour normaliser les hauteurs des barres.
 */
$maxDailyVisits = (!isset($dailyVisits['error']) && !empty($dailyVisits)) ? max($dailyVisits) : 0;

/**
 * @var int Hauteur maximale pour les barres des graphiques de visites quotidiennes (en pixels).
 */
$maxBarHeight = 300;

// Fin de la bufferisation
ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang ?>" lang="<?= $lang ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Statistiques - SKY METEO</title>
    <link rel="stylesheet" type="text/css" href="css/<?= htmlspecialchars($cssFile) ?>" />
    <link rel="icon" type="image/png" href="images/logo-favicon.png" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style type="text/css">
        /* Statistiques spécifiques */
        .stats-block {
            background-color: rgba(255, 255, 255, 0.85); /* Light theme */
            padding: 30px;
            margin: 40px auto;
            max-width: 1100px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        body.dark .stats-block {
            background-color: rgba(10, 10, 10, 0.8); /* Dark theme */
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(255, 255, 255, 0.05);
        }
        .stats-block h2 {
            font-size: 26px;
            margin-bottom: 15px;
            color: #1e3c72; /* Light theme */
        }
        body.dark .stats-block h2 {
            color: #ffd700; /* Dark theme */
        }
        .stats-block p {
            font-size: 17px;
            margin-bottom: 25px;
            color: #333; /* Light theme */
        }
        body.dark .stats-block p {
            color: #ccc; /* Dark theme */
        }
        .last-visit {
            background-color: rgba(255, 255, 255, 0.9); /* Light theme */
            padding: 20px;
            margin: 20px auto;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        body.dark .last-visit {
            background-color: rgba(25, 25, 25, 0.95); /* Dark theme */
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
        }
        .last-visit p {
            margin: 10px 0;
            font-size: 16px;
            color: #000000; /* Light theme */
        }
        body.dark .last-visit p {
            color: #ffffff; /* Dark theme */
        }
        .stats-chart, .daily-visits-chart {
            max-width: 800px;
            margin: 30px auto;
            background-color: rgba(255, 255, 255, 0.9); /* Light theme */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            height: 400px; /* Fixed height for Chart.js */
        }
        body.dark .stats-chart, body.dark .daily-visits-chart {
            background-color: rgba(25, 25, 25, 0.95); /* Dark theme */
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
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
<body class="stats-page">
<a id="top"></a>
<div class="wrapper">
    <main>
        <!-- Bloc 1 : Statistiques par ville -->
        <section class="block stats-block">
            <h2>Statistiques des consultations par ville</h2>
            <p>Consultez le nombre de recherches effectuées par ville sur SKY METEO.</p>

            <!-- Dernière ville consultée -->
            <div class="last-visit">
                <?php if ($lastCity): ?>
                    <p><strong>Dernière ville consultée :</strong> <?= htmlspecialchars($lastCity['city']) ?></p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($lastCity['date']) ?></p>
                    <p><strong>Adresse IP :</strong> <?= htmlspecialchars($lastCity['ip']) ?></p>
                <?php else: ?>
                    <p>Aucune ville consultée récemment.</p>
                <?php endif; ?>
            </div>

            <!-- Diagramme des statistiques par ville -->
            <div class="stats-chart">
                <?php if (isset($cityStats['error'])): ?>
                    <p class="error-message">Erreur : Impossible de lire les statistiques.</p>
                <?php elseif (empty($cityStats)): ?>
                    <p class="error-message">Aucune donnée disponible.</p>
                <?php else: ?>
                    <canvas id="cityChart"></canvas>
                    <script>
                        try {
                            const cityCtx = document.getElementById('cityChart').getContext('2d');
                            const cityLabels = <?= json_encode(array_keys($cityStats), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS) ?>;
                            const cityData = <?= json_encode(array_values($cityStats), JSON_NUMERIC_CHECK) ?>;
                            const isDarkTheme = document.body.classList.contains('dark');

                            if (!cityLabels || !cityData) {
                                throw new Error('Données de ville non valides');
                            }

                            new Chart(cityCtx, {
                                type: 'bar',
                                data: {
                                    labels: cityLabels,
                                    datasets: [{
                                        label: 'Consultations par ville',
                                        data: cityData,
                                        backgroundColor: isDarkTheme
                                            ? 'rgba(255, 215, 0, 0.8)' // Gold for dark theme
                                            : 'rgba(30, 60, 114, 0.8)', // Adapted #1e3c72 for light theme
                                        borderColor: isDarkTheme
                                            ? 'rgba(255, 215, 0, 1)'
                                            : 'rgba(30, 60, 114, 1)',
                                        borderWidth: 1,
                                        borderRadius: 4,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            labels: {
                                                color: isDarkTheme ? '#ffffff' : '#000000',
                                                font: { size: 14 }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDarkTheme ? 'rgba(25, 25, 25, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                            titleColor: isDarkTheme ? '#ffffff' : '#000000',
                                            bodyColor: isDarkTheme ? '#ffffff' : '#000000',
                                            borderColor: isDarkTheme ? '#ffd700' : '#1e3c72',
                                            borderWidth: 1
                                        }
                                    },
                                    scales: {
                                        x: {
                                            ticks: {
                                                color: isDarkTheme ? '#ffffff' : '#000000',
                                                font: { size: 12 },
                                                maxRotation: 45,
                                                minRotation: 45
                                            },
                                            grid: {
                                                display: false
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                color: isDarkTheme ? '#ffffff' : '#000000',
                                                font: { size: 12 },
                                                stepSize: Math.ceil(Math.max(...cityData) / 5) || 1
                                            },
                                            grid: {
                                                color: isDarkTheme ? 'rgba(255, 255, 255, 0.2)' : 'rgba(0, 0, 0, 0.2)'
                                            }
                                        }
                                    },
                                    animation: {
                                        duration: 1000,
                                        easing: 'easeOutQuad'
                                    }
                                }
                            });
                        } catch (e) {
                            console.error('Erreur lors de la création du graphique des villes :', e);
                            document.querySelector('.stats-chart').innerHTML = '<p class="error-message">Erreur lors du chargement du graphique.</p>';
                        }
                    </script>
                <?php endif; ?>
            </div>
        </section>

        <!-- Bloc 2 : Statistiques des visites par jour -->
        <section class="block stats-block">
            <h2>Statistiques des visites par jour</h2>
            <p>Consultez le nombre de visites sur les 5 derniers jours.</p>

            <!-- Graphe des visites par jour -->
            <div class="daily-visits-chart">
                <?php if (isset($dailyVisits['error'])): ?>
                    <p class="error-message">Erreur : Impossible de lire les statistiques.</p>
                <?php elseif (empty($dailyVisits) || max($dailyVisits) == 0): ?>
                    <p class="error-message">Aucune donnée disponible pour les 5 derniers jours.</p>
                <?php else: ?>
                    <canvas id="dailyChart"></canvas>
                    <script>
                        try {
                            const dailyCtx = document.getElementById('dailyChart').getContext('2d');
                            const dailyLabels = <?= json_encode(array_keys($dailyVisits), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS) ?>;
                            const dailyData = <?= json_encode(array_values($dailyVisits), JSON_NUMERIC_CHECK) ?>;
                            const isDarkTheme = document.body.classList.contains('dark');

                            if (!dailyLabels || !dailyData || dailyLabels.length !== 5 || dailyData.length !== 5) {
                                throw new Error('Données quotidiennes non valides ou incomplètes');
                            }

                            console.log('Daily Labels:', dailyLabels); // Debug
                            console.log('Daily Data:', dailyData); // Debug

                            new Chart(dailyCtx, {
                                type: 'bar',
                                data: {
                                    labels: dailyLabels,
                                    datasets: [{
                                        label: 'Visites par jour',
                                        data: dailyData,
                                        backgroundColor: [
                                            'rgba(255, 138, 128, 0.8)', // J-4: Rouge clair
                                            'rgba(76, 175, 80, 0.8)',   // J-3: Vert doux
                                            'rgba(255, 204, 128, 0.8)', // J-2: Orange clair
                                            'rgba(66, 165, 245, 0.8)',  // J-1: Bleu clair
                                            'rgba(255, 213, 79, 0.8)'   // J: Jaune doux
                                        ],
                                        borderColor: [
                                            'rgba(255, 138, 128, 1)',
                                            'rgba(76, 175, 80, 1)',
                                            'rgba(255, 204, 128, 1)',
                                            'rgba(66, 165, 245, 1)',
                                            'rgba(255, 213, 79, 1)'
                                        ],
                                        borderWidth: 1,
                                        borderRadius: 4,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                            labels: {
                                                color: isDarkTheme ? '#ffffff' : '#000000',
                                                font: { size: 14 }
                                            }
                                        },
                                        tooltip: {
                                            backgroundColor: isDarkTheme ? 'rgba(25, 25, 25, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                            titleColor: isDarkTheme ? '#ffffff' : '#000000',
                                            bodyColor: isDarkTheme ? '#ffffff' : '#000000',
                                            borderColor: isDarkTheme ? '#ffd700' : '#1e3c72',
                                            borderWidth: 1
                                        }
                                    },
                                    scales: {
                                        x: {
                                            ticks: {
                                                color: isDarkTheme ? '#ffffff' : '#000000',
                                                font: { size: 12 }
                                            },
                                            grid: {
                                                display: false
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                color: isDarkTheme ? '#ffffff' : '#000000',
                                                font: { size: 12 },
                                                stepSize: Math.ceil(Math.max(...dailyData) / 5) || 1
                                            },
                                            grid: {
                                                color: isDarkTheme ? 'rgba(255, 255, 255, 0.2)' : 'rgba(0, 0, 0, 0.2)'
                                            }
                                        }
                                    },
                                    animation: {
                                        duration: 1000,
                                        easing: 'easeOutQuad'
                                    }
                                }
                            });
                        } catch (e) {
                            console.error('Erreur lors de la création du graphique quotidien :', e);
                            document.querySelector('.daily-visits-chart').innerHTML = '<p class="error-message">Erreur lors du chargement du graphique.</p>';
                        }
                    </script>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php require_once 'include/footer.inc.php'; ?>

    <a href="#top" class="back-to-top" title="Retour en haut">⬆️</a>
</div>
</body>
</html>