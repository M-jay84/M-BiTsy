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
$person = $data['tmdb']->getPerson($data['id']); ?>

<div class="row">
    <p class='text-center'><legend><b><?php echo $person->getName(); ?></b></legend></p>
    
    <div class="col-12 col-md-2"> <?php
    echo '<img src="' . $data['tmdb']->getImageURL('w185') . $person->getProfile() . '"/>'; ?>
    </div>
    
    <div class="col-12 col-md-4"> <?php
    echo 'Birthday<br>' . $person->getBirthday() . '</br><br>'; ?>
    </div>
    
    <div class="col-12 col-md-3"> <?php
    // Get the movie roles
    $movieRoles = $person->getMovieRoles();
    echo '<b>Movie Roles</b><br>';
    foreach ($movieRoles as $movieRole) {
        echo $movieRole->getCharacter() . ' in <a href="'.URLROOT.'/moviedatabase/movies?id=' . $movieRole->getMovieID() . '">' . $movieRole->getMovieTitle() . '</a><br>';
    } ?>
    </div>
    
    <div class="col-12 col-md-3"> <?php
    //  Get the show roles
    $tvShowRoles = $person->getTVShowRoles();
    echo '<b>Show Roles</b><br>';
    foreach ($tvShowRoles as $tvShowRole) {
        echo $tvShowRole->getCharacter() . ' in <a href="'.URLROOT.'/moviedatabase/shows?id=' . $tvShowRole->getTVShowID() . '">' . $tvShowRole->getTVShowName() . '</a><br>';
    } ?>
    </div>

</div>