[README.md](https://github.com/user-attachments/files/28879827/README.md)
# Projet SkyMétéo2BIS - Développement Web

## Description
Ce projet a été réalisé dans le cadre de l'UE **Développement Web** de la Licence 2 Informatique (L2-I), S4, année universitaire 2024-2025.  
**SkyMétéo2BIS** est un site web dédié aux prévisions météorologiques en France métropolitaine. Il permet aux utilisateurs de consulter les prévisions météo à 5 jours, à partir d’une navigation intuitive basée sur la sélection d’une région, d’un département, puis d’une ville.  
Le projet met en œuvre les technologies **HTML5**, **CSS3**, **PHP 8**, et des appels à des **API REST** (JSON et XML) tout en respectant les principes d’ergonomie, d’accessibilité et de performance.

---

## Contenu du site

- **`index.php`** : Page d’accueil avec image aléatoire et accès à la carte interactive.
- **`map.php`** : Sélection d’une région via une carte HTML `<map>` puis choix du département.
- **`select_ville.php`** : Choix de la ville via liste déroulante dynamique.
- **`ville.php`** : Affichage des prévisions météo (jour en cours + 4 jours suivants).
- **`tech.php`** : Page technique présentant des appels à des API JSON (NASA APOD) et XML (GeoPlugin).
- **`tech_details.php`** : Donne des détails supplémentaires via ipinfo.io et whatismyip.com.
- **`tech_stats.php`** : Statistiques des villes les plus consultées avec affichage graphique.
- **`functions.php`** : Fonctions PHP pour les appels API, gestion des cookies, CSV, etc.
- **`css/style.css`** et **`css/style_alternatif.css`** : Thèmes jour/nuit.
- **`photos/`** : Images affichées aléatoirement sur l’accueil.
- **`csv/villes_consultees.csv`** : Fichier de stockage des consultations.

---

## Fonctionnalités principales

### 🔍 Navigation et UX
- Carte HTML interactive pour le choix de la région (`<map>`, `<area>`, `<usemap>`).
- Menus déroulants pour le choix du département et de la ville.
- Fils d’Ariane pour se repérer dans la navigation.
- Cookies pour sauvegarder les préférences utilisateur.

### 🌦️ Prévisions météo
- Données récupérées via **WeatherAPI** en format JSON.
- Météo actuelle + prévisions sur les 4 jours suivants.
- Affichage dynamique avec date, température, condition, icône météo.

### ⚙️ API Techniques
- **NASA APOD (JSON)** : image/vidéo du jour et explication.
- **GeoPlugin (XML)** : géolocalisation par IP.
- **ipinfo.io & whatismyip.com** : infos complémentaires via flux JSON et XML.

### 📊 Statistiques
- CSV des villes consultées en PHP côté serveur.
- Affichage d’un histogramme dynamique avec **Chart.js**.

### 🎨 Personnalisation
- Thème jour/nuit mémorisé via cookie.
- Dernière ville consultée conservée côté client.
- Affichage aléatoire d’une image sur l’accueil (`<figure>`, `<figcaption>`).

---

## Technologies utilisées

- **HTML5** : Structuration sémantique, carte interactive.
- **CSS3** : Mise en page responsive, thèmes clairs/sombres.
- **PHP 8** : Appels API, gestion fichiers CSV, traitement côté serveur.
- **JavaScript** : Intégration Chart.js, gestion cookies.
- **APIs** :  
  - [WeatherAPI](https://www.weatherapi.com/)  
  - [NASA APOD](https://api.nasa.gov/)  
  - [GeoPlugin](https://www.geoplugin.com/)  
  - [ipinfo.io](https://ipinfo.io/)  
  - [whatismyip.com](https://www.whatismyip.com/)

---

## Installation

1. Cloner ou extraire l’archive `SkyMétéo2BIS.zip`.
2. Déployer les fichiers sur un serveur compatible **PHP 8**.
3. Configurer vos **clés API** dans les fichiers `functions.php` ou `.env` si utilisé.
4. Vérifier l’emplacement des fichiers CSV dans `/csv/`.
5. Ajouter des images dans `/photos/` pour l’image aléatoire.
6. S’assurer que le serveur a **les permissions d’écriture** sur `csv/villes_consultees.csv`.

---

## Hébergement

Le site est hébergé via **AlwaysData** :  
- https://moussaoui.alwaysdata.net/  
- https://daniaham.alwaysdata.net/

---

## Auteurs

Ce projet a été réalisé par deux étudiants en **L2 Informatique** dans le cadre de l’UE **Développement Web** :

- **Imane MOUSSAOUI**  
- **Dania HAMITOUCHE**

---

## Contact

📧 [contact@skymeteo.com](mailto:contact@skymeteo.com)  
🏢 2 avenue Adolphe-Chauvin, 95302 Cergy-Pontoise  
📞 +33 1 34 25 60 00  

---

## Livrables

- Code source complet du projet.
- Documentation HTML générée avec `phpdoc`.
- Rapport de projet au format PDF/ODT.
- Vidéo de démonstration scénarisée (3 minutes max).
- Archive finale : `grp_prj_01D_MOUSSAOUI_HAMITOUCHE.zip`

---

© 2025 SkyMétéo2BIS — Tous droits réservés.
