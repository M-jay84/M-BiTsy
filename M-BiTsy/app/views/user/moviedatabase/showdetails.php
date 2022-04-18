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
<div class="text-center"> <?php
    TMDBS::getSerie($data['id']); ?>
                
    <b> Plot : </b><?php echo $_data["plot"] ?><br><br>
    <b> Actors : </b><br>
    <div class='row'>  <?php
       $casting = explode('&', $_data["actor"]);
       for ($i = 0; $i <= 3; $i++) {
         list($pseudo, $role, $image) = explode("*", $casting[$i]);;
         print("<div class='col-3'>".$pseudo . "<br>");
         print(" <img class='avatar3' src='" . $image . "' /><br>");
         print("".$role . "<br></div>");
       } ?>
    </div>
    <b> Status : </b><?php echo $_data["status"] ?> <br>
    <b> Date : </b><?php echo $_data["date"] ?> <br>
    <b> Creator : </b><?php echo $_data["creator"] ?><br>
    <b> Genre : </b><?php echo $_data["genre"] ?><br>
    <b> Seasons : </b><?php echo $_data["season"] ?> (<?php echo $_data["episode"] ?> épisodes) <br>
    <br>
</div>
</div>