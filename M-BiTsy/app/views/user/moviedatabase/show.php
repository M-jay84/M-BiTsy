<div class="ttform">
    <form method="post" action="<?php echo URLROOT; ?>/moviedatabase/submit" autocomplete="off">

    <div class="text-center">
    <label for="inputsearch" class="col-form-label"><?php echo Lang::T("Search TMDB (The Movie Database)"); ?></label>
       <input id="inputsearch" type="text" class="form-control" name="inputsearch" minlength="3" maxlength="40" required autofocus><br>
        </div>

    <div class="text-center">
    <button id="input" name="input"type="submit" class="btn ttbtn" value="movie"><?php echo Lang::T("Search Movie"); ?></button>
	<button id="input" name="input"type="submit" class="btn ttbtn" value="show"><?php echo Lang::T("Search Show"); ?></button>
	<button id="input" name="input" type="submit" class="btn ttbtn" value="person"><?php echo Lang::T("Search Person"); ?></button>
	</div>

    </form>
</div><br>

<div class="ttform">
<div class="text-center">
    <a id="searchTVShow"><h3>Search TVShow</h3></a> <?php
$tvShows = $data['tmdb']->searchMovie($data['name']);
foreach ($tvShows as $tvShow) {
    echo $tvShow->getTitle() . ' <a href="'.URLROOT.'/moviedatabase/shows?id=' . $tvShow->getID() . '"><b>Link</b></a><br>';
} ?>
</div>
</div>