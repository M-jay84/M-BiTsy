<div class="ttform">
    <form method="post" action="<?php echo URLROOT; ?>/moviedatabase/submit" autocomplete="off">
    <input type='hidden' name='search' value='<?php echo Input::get('inputsearch') ?>' />
    
    <div class="text-center">
        Search The Movie Database Website <a href='https://www.themoviedb.org/'>www.themoviedb.org</a><br>
       <label for="inputsearch" class="col-form-label"><?php echo Lang::T("Search TMDB (The Movie Database)"); ?></label>
       <input id="inputsearch" type="text" class="form-control" name="inputsearch" minlength="3" maxlength="40" value='<?php echo Input::get('inputsearch') ?>' required autofocus><br>
    </div>

    <div class="text-center">
    <button id="input" name="input"type="submit" class="btn ttbtn" value="movie"><?php echo Lang::T("Search Movie"); ?></button>
	<button id="input" name="input"type="submit" class="btn ttbtn" value="show"><?php echo Lang::T("Search Show"); ?></button>
	<button id="input" name="input" type="submit" class="btn ttbtn" value="person"><?php echo Lang::T("Search Person"); ?></button>
	</div>

    </form>
</div><br> <?php

// Get TMDB Api
$tmdb = new TMDB(); // , true
$movie = $tmdb->getMovie($data['id']); ?>

<div class="row">
    <p class='text-center'><legend><b><?php echo $movie->getTitle(); ?></b></legend></p>
    
    <div class="col-12 col-md-2"> <?php
    echo '<img src="'. $tmdb->getImageURL('w185') . $movie->getPoster() .'"/>'; ?>
    </div>
    
    <div class="col-12 col-md-6"> <?php
    echo '<b>'. $movie->genre() .'</b><br><br>';
    echo '<b>'. $movie->getTagline() .'</b><br><br>';
    echo '<b>'. $movie->getplot() .'</b><br><br>';
    echo '<b>Duration '. $movie->duration() .'</b><br>'; ?>
    </div>
    
    <div class="col-12 col-md-4"> <?php
    print("<embed src='https://www.youtube.com/v/" . str_replace("watch?v=", "v/", htmlspecialchars($movie->getTrailer())) . "' type=\"application/x-shockwave-flash\"  height=\"410\" width='100%'></embed>"); ?>
    </div>
    
    <div class="col-12 col-md-9"> <?php
    $actors = $movie->actor();
    $casting = $actors;
    $casting_role = $casting[0];
    $casting_role = explode('*', $casting_role);
    $casting_nom = $casting[1];
    $casting_nom = explode('+', $casting_nom);
    $casting_img = $casting[2];
    $casting_img = explode('&', $casting_img);
    $casting_id = $casting[3];
    $casting_id = explode('&', $casting_id);
    print("<div class='row'>");
    for ($i = 0; $i <= 3; $i++) {
        print("<div class='col-3'>");
        echo "<a href='".URLROOT."/moviedatabase/person?id=".$casting_id[$i]."'><img class='avatar3' src='" . $casting_img[$i] . "' /></a><br>".$casting_nom[$i]."<br>".$casting_role[$i]."&nbsp;";
        print("</div>");
    }
    print("</div>"); ?>
    </div>

</div>