<table class='table table-striped table-bordered table-hover'>
    <thead>
    <tr><td>
    <?php $elapsed = TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($data["added"])); ?>
    <b><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $data['answeredby']; ?>"><?php echo Users::coloredname($data['answeredby']); ?></a>
    </b> answered this message sent by <?php echo Users::coloredname($data['sender']); ?><br>
    <b>Subject: <?php echo $data['subject']; ?></b>
    <br><b>Answer:</b> <?php echo format_comment($data['answer']); ?>
</td></tr></table>