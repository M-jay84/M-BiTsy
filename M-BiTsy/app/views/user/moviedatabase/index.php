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
</div>