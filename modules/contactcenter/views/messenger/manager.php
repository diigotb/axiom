<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <!-- <?php echo '<pre>';
                        print_r($pages);
                        echo '</pre>';
                        ?> -->

                        <div class="card-messenger-container">
                            <?php foreach ($pages as $page) { ?>
                                <div class="card-messenger">
                                    <a href="<?php echo admin_url('contactcenter/messengerchat/' . $page['id']); ?>">
                                        <div class="card-messenger-header">
                                            <i class="fa-brands fa-facebook-messenger"></i>
                                            <?php if ($page['instagramId']) { ?>
                                                <i class="fa-brands fa-instagram"></i>
                                            <?php } ?>
                                        </div>
                                        <div class="card-messenger-body">
                                            <h5><?php echo $page['name']; ?></h5> 
                                            <p>#ID <?php echo $page['id']; ?></p>                                                                                                                         
                                            <p><?php echo $page['category_list'][0]['name']; ?></p>                                                                                                                         
                                        </div>
                                    </a>
                                </div>
                            <?php } ?>


                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>

</html>