<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1.0,user-scalable=no" />
  <link rel="stylesheet" href="<?= base_url() . 'assets/stylesheets/reset.css' ?>" />
  <link rel="stylesheet" href="<?= base_url() . 'assets/stylesheets/application.css' ?>" />
  <link rel="stylesheet" href="<?= base_url() . 'assets/stylesheets/maps.css' ?>" />
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  <script src="<?= base_url() . 'assets/javascripts/jquery-2.js' ?>"></script>
  <script src="<?= base_url() . 'assets/javascripts/application.js' ?>"></script>
  <title>Disaster Person Tracker</title>
</head>

<body>
  <div id="map-canvas"></div>

  <?= form_open('maps/search', array('id' => 'search-box')); ?>
    <input type="text" id="search" placeholder="Search a person" autocomplete="off" spellcheck="false" />
    <input type="submit" />
  <?= form_close(); ?>

  <!--
  <div class="info-window">
    <h3>Arnelle Balane</h3>
    <div class="status">
      <p>Alive but needs immediate medical care</p>
      <time>November 19, 2013</time>
    </div>
    <div class="previous-statuses hidden">
      <div class="status">
        <p>Barely living</p>
        <time>November 18, 2013</time>
      </div>
      <div class="status">
        <p>Barely living</p>
        <time>November 17, 2013</time>
      </div>
    </div>
    <button data-behavior="toggle-previous-statuses">Show Previous Statuses</button>
  </div>
  -->
</body>
</html>