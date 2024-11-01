<?php 
 require_once(RMPF_PATH . 'admin/view/List.php');
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e('Board', 'wp-roadmap'); ?></h1>
    <button type="button" class="page-title-action" data-toggle="modal" data-target="#addNewItemModal">Add New Board</button>
    <form method="post">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
        <?php
        $table = new Rmpf_List_Table();
        $table->prepare_items();
        $table->search_box('search', 'search_id');
        $table->display();
        ?>
    </form>
</div>

<div class="modal fade" id="addNewItemModal" tabindex="-1" role="dialog" aria-labelledby="addNewItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewItemModalLabel"><?php esc_html_e('Add New Board', 'wp-roadmap'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addNewItemForm" method="post">
                    <div class="form-group">
                        <label for="new_item_name"><?php esc_html_e('Board Name', 'wp-roadmap'); ?></label>
                        <input type="text" class="form-control" id="new_item_name" name="new_item_name" placeholder="<?php esc_attr_e('Enter Board Name', 'wp-roadmap'); ?>" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            <button type="submit" id="addform" class="btn btn-primary" form="addNewItemForm"><?php esc_html_e('Save', 'wp-roadmap'); ?></button>
            </div>
        </div>
    </div>
</div>
