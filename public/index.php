<?php

/*
 * Author: Inazo
 * Website: https://www.kanjian.fr
 * GitHub: https://github.com/inaz0/fzoc
 * Youtube Channel: https://youtube.com/@kanjian_fr
 * X: https://www.x.com/bsmt_nevers
 * Instagram: https://www.instagram.com/kanjian_fr/
 * Buy me a coffee: https://buymeacoffee.com/inazo
 */

 ?>

<!DOCTYPE html>
<html lang="fr">
<head>

  <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta charset="utf-8">
  <title>FlipperZero online compilator</title>

  <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="stylesheet" href="assets/css/normalize.css">
  <link rel="stylesheet" href="assets/css/skeleton.css">
  <link rel="stylesheet" href="assets/css/custom.css">


  <!-- Favicon
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <link rel="icon" type="image/png" href="../../dist/images/favicon.png">

</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

  <div class="section hero">
    <div class="container">
      <div class="row">
        <div class="one-half column">
          <h1 class="hero-heading">Compil your application</h1>
          <form action="" method="post">
            <label>URL du dépôt git : </label><input type="text" name="git_url" />
            <label>Firmware cible : </label><select name="firmware_target"><option value="1">Official</option><option value="2">Momentum</option></select>
            <label>Firmware version (latest of) : </label><select name="git_branch"><option value="1">Release</option><option value="2">Dev</option></select>
          </form>
          <a class="button button-primary" href="https://demo-asvs.keikai.eu" target="_blank">Launch compil!</a>
        </div>        
      </div>
    </div>
  </div>

  <div class="section values">
    <div class="container">
      <div class="row">
        <div class="one-third column value">
          <h2 class="value-multiplier">4.0.2</h2>
          <h5 class="value-heading">Oldest version</h5>
          <p class="value-description">Still avaible in our tool.</p>
        </div>

	<div  class="one-third column value">
          <h2 class="value-multiplier">See in action</h2>
          <h5 class="value-heading">Take a look to our:</h5>
	  <p><a  class="button button-primary" href="https://www.youtube.com/playlist?list=PLq_UnUtYZ15fUy2ilIvSS8ID1l9_XpjUW">Youtube videos</a></p>
	</div>

        <div class="one-third column value">
          <h2 class="value-multiplier">4.0.3</h2>
          <h5 class="value-heading">Current version</h5>
          <p class="value-description">Recommended version.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="section get-help">
    <div class="container">
      <h3 class="section-heading">Need to try the solution?</h3>
      <p class="section-description">Got a try with our demo version below:</p>
      <a class="button button-primary" href="https://demo-asvs.keikai.eu" target="_blank">Go to demo</a>
    </div>
  </div>

  <div class="section categories">
    <div class="container">
      <h3 class="section-heading">Want to support?</h3>
      <p class="section-description">Pay what you want, just paid a coffee here:</p>
      <p><a id="byMeACoffee" class="button button-primary" href="https://www.buymeacoffee.com/inazo" target="_blank">By me a coffee</a></p>
      
      <h3 class="section-heading">Follow me:</h3>
      <p>
        <a href="https://twitter.com/bsmt_nevers" target="_blank"><img src="assets/images/icons8-twitter-48.png" alt="Twitter" /></a>
        <a href="https://www.youtube.com/@kanjian_fr" target="_blank"><img src="assets/images/icons8-youtube-48.png" alt="Youtube" /></a>
      </p>
      <p>
        <a id="showLegalMentions" href="legal.php">Legal mentions</a>
      </p>
    </div>
  </div>


<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>
