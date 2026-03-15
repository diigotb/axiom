<li role="presentation">
    <a href="#lead_ai_interactions" aria-controls="lead_ai_interactions" role="tab" data-toggle="tab">
        <i class="fa fa-robot"></i> <?php echo _l('lead_ai_interactions'); ?>
        <?php if (isset($ai_interactions_count) && $ai_interactions_count > 0) { ?>
        <span class="badge"><?php echo $ai_interactions_count; ?></span>
        <?php } ?>
    </a>
</li>
