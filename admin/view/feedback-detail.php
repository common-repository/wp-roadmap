<a data-toggle="modal" href="#wp-detail" class="btn btn-primary btn-lg" style="display:none;"></a>
<div class="modal fade" id="wp-detail">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header display">
        <h4 class="modal-title"><?php echo esc_html_e( 'Feedback Detail', 'wp-roadmap' ); ?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      <div class="modal-body">
        <table class="table table-striped">
            <thead>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo esc_html_e( 'Feedback', 'wp-roadmap' ); ?> : </td>
                    <td id="wp_detail_feedback"></td>
                </tr>
                <tr>
                    <td><?php echo esc_html_e( 'Description', 'wp-roadmap' ); ?>:</td>
                    <td id="wp_detail_feedback_description"></td>
                </tr>
                <tr>
                    <td><?php echo esc_html_e( 'Status', 'wp-roadmap' ); ?>:</td>
                    <td id="wp_detail_feedback_status"></td>
                </tr>
                <tr>
                    <td><?php echo esc_html_e( 'Created Date', 'wp-roadmap' ); ?>:</td>
                    <td id="wp_detail_feedback_date"></td>
                </tr>
                <tr>
                    <td><?php echo esc_html_e( 'Upvote', 'wp-roadmap' ); ?>:</td>
                    <td id="wp_detail_feedback_upvote"></td>
                </tr>
            </tbody>
        </table>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->