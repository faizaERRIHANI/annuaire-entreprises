# Annuaire d'Entreprises

Projet étudiant développé en **PHP / MySQL** avec **PDO** et **Bootstrap**.

## Description

Cette application permet de gérer un annuaire d’entreprises avec plusieurs fonctionnalités :

- ajout d’entreprise
- modification d’entreprise
- suppression d’entreprise
- recherche par nom
- filtre par catégorie
- affichage des détails d’une entreprise
- ajout d’avis avec note sur 5 étoiles
- upload du logo de l’entreprise

## Technologies utilisées

- PHP
- MySQL
- PDO
- Bootstrap 5
- HTML
- CSS
- XAMPP
- Git
- GitHub

## Structure du projet

- assets/
- avis/
- config/
- entreprises/
- includes/
- uploads/logos/
- database.sql
- index.php

## Base de données

Nom de la base de données : `annuaire_entreprises`

Tables principales :

- `entreprises`
- `avis`

## Fonctionnalités principales

### Gestion des entreprises

- créer une entreprise
- afficher la liste des entreprises
- afficher les détails d’une entreprise
- modifier une entreprise
- supprimer une entreprise

### Recherche

- recherche par nom
- filtre par catégorie

### Avis

- ajout d’un avis
- note de 1 à 5
- calcul automatique de la note moyenne
- affichage du nombre d’avis

### Logo

- upload d’un logo
- affichage du logo dans la fiche détail
- affichage du logo dans la liste

## Installation du projet

1. Copier le dossier dans `C:\xampp\htdocs\`
2. Démarrer Apache et MySQL dans XAMPP
3. Créer la base de données `annuaire_entreprises`
4. Importer le fichier `database.sql`
5. Vérifier les paramètres de connexion dans `config/database.php`
6. Ouvrir le projet dans le navigateur : `http://localhost/annuaire-entreprises/`

## Auteur

Projet réalisé par **Faiza ERRIHANI**.