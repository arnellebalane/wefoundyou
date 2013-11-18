<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1.0,user-scalable=no" />
  <link rel="stylesheet" href="<?= base_url() . 'assets/stylesheets/reset.css' ?>">
  <link rel="stylesheet" href="<?= base_url() . 'assets/stylesheets/application.css' ?>">
  <link rel="stylesheet" href="<?= base_url() . 'assets/stylesheets/maps.css' ?>">
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  <script src="<?= base_url() . 'assets/javascripts/jquery-2.js' ?>"></script>
  <script src="<?= base_url() . 'assets/javascripts/application.js' ?>"></script>
  <title>Disaster Person Tracker</title>
</head>

<body>
  <div id="map-canvas"></div>

  <?= form_open('maps/search', array('id' => 'search-box')); ?>
    <input type="text" id="search" autocomplete="off" spellcheck="false" />
    <input type="submit" />
  <?= form_close(); ?>
</body>
</html>