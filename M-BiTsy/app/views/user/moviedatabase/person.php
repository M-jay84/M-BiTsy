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
    <a id="searchPerson"><h3>Search Person</h3></a> <?php
    $persons = $data['tmdb']->searchPerson($data['name']);
    foreach ($persons as $person) {
        echo $person->getName() . ' <a href="'.URLROOT.'/moviedatabase/person?id=' . $person->getID() . '"><b>Link</b></a><br>';
    } ?>
</div>
</div>