<?php
class Ajax
{

    public function __construct()
    {
        Auth::user(0, 2);
    }

    // User search on message create
	public function index()
    {
        if(!empty($_POST["keyword"])) {
            $result = DB::run("SELECT * FROM users WHERE username like '" . $_POST["keyword"] . "%' ORDER BY username LIMIT 0,6");
            if(!empty($result)) {
                ?>
                <ul id="country-list">
                <?php foreach($result as $user) { ?>
                    <li onClick="userCountry('<?php echo $user["id"]; ?>');"><?php echo $user["username"]; ?></li>
                <?php } ?>
                </ul>
                <?php
            }
        }
    }

}