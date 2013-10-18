<h3><?php echo $this->method_title ;?></h3>

    <?php if ( $this->is_valid_for_use() ) : ?>
	<div id="wc_get_started" class="paygate">
		<span class="main"><?php  _e( 'Get started PayGate', 'wc_korea_pack' ); ?></span>
		<span>
			<a href="https://admin.paygate.net/" target="paygate_admin" ><?php _e('Paygate 상점관리자 ', 'wc_korea_pack');?></a>
		</span>

		<p><a href="http://www.paygate.net/apply/general.php" target="_blank" class="button button-primary"><?php _e( 'Join', 'wc_korea_pack' ); ?></a> </p>
	</div>
	<table class="form-table">
		<?php $this->generate_settings_html(); ?>
	</table><!--/.form-table-->
    <?php else : ?>
            <div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'wc_korea_pack' ); ?></strong>: <?php _e( 'This Gateway does not support your store currency.', 'wc_korea_pack' ); ?></p></div>
    <?php endif;?>
