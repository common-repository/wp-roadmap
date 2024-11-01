<div class="wp-feedback-roadmap">
    <div class="container-fluid">
        <div class="box-content">
            <div class="header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="logo-detail d-flex align-items-center">
                        <div class="logo-title ml-2">
                            <h3 class="header-title"><?php esc_html_e("WP Roadmap - Product Feedback Board", "wp-roadmap") ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="settings-form" style="margin-top: 20px">
                <form method="POST" id="wp-feedback-roadmap-settings" name="wp-feedback-roadmap-settings">
                    <div class="wp-feedback-roadmap-main">
                        <div class="wp-feedback-roadmap-tabs">
                            <ul id="main">
                                <li class="tab-active"><a class="tab" href="#status"><?php esc_html_e("Manage Status", "wp-roadmap") ?></a></li>
                                <li><a class="tab" href="#general"><?php esc_html_e("General Settings", "wp-roadmap") ?></a></li>
                            </ul>
                            <div class="wp-feedback-roadmap-tab">
                                <div id="status" class="wp-feedback-roadmap-tab-detail" >
                                    <?php  require_once(RMPF_PATH.'admin/view/status-settings.php'); ?>
                                </div>
                                <div id="general" class="wp-feedback-roadmap-tab-detail active">
                                    <?php  require_once(RMPF_PATH.'admin/view/general-settings.php'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>