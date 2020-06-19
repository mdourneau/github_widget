<h5><?= $repository ?> by <?= $user ?> sur la branche : <?= $branch ?></h5>
        <ul>
        <?php       
        for($i = 0 ;  $i < $commitsdisplay ; $i++) : ?>
            <li><?= $commits[$i]['commit']['message'] ?><br><small>comitted by <?= $commits[$i]['commit']['author']['name'] ?></small> <small>sur la branche <?= $branch ?></small></li>
        <?php endfor; ?>
        </ul>