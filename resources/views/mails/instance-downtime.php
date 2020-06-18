<div>
  <p>Hello,</p>
  <p>Please be informed that instance <b><?=$data['instance']?></b> 
  <?php if (isset($data['project'])) { ?>
    for project <b><?=$data['project']?></b>
        <?php
  } ?>
        , will be unavailable approximately 
        from <?=$data["start_datetime"]?> 
        to <?=$data["end_datetime"]?> 
        in <?=$data['timezone']?> time.</p>
  <p>Aditional details: <?=$data['description']?>.</p>
</div>