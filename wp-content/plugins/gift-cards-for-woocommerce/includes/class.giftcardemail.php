<?php

/**
 * Gift Card Email Functions
 *
 * @package     Gift-Cards-for-Woocommerce
 * @copyright   Copyright (c) 2014, Ryan Pletcher
 *
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class KODIAK_Giftcard_Email {

	public function sendEmail ( $post ) {

		$blogname 		= wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$subject 		= apply_filters( 'woocommerce_email_subject_gift_card', sprintf( '[%s] %s', $blogname, __( 'Gift Card Information', 'kodiak-giftcards' ) ), $post->post_title );
		$sendEmail 		= get_bloginfo( 'admin_email' );
		$headers 		= array('Content-Type: text/html; charset=UTF-8');

		ob_start();

		$mailer 		= WC()->mailer();
		$email 			= new KODIAK_Giftcard_Email();

		echo '<style >';
		wc_get_template( 'emails/email-styles.php' );
		echo '</style>';

	  	$email_heading 	= __( 'New gift card from ', 'kodiak-giftcards' ) . $blogname;
	  	$email_heading 	= apply_filters( 'rpgc_emailSubject', $email_heading );
	  	$toEmail		= wpr_get_giftcard_to_email( $post->ID );

	  	$theMessage 	= $email->sendGiftcardEmail ( $post->ID );

		$theMessage 	= apply_filters( 'rpgc_emailContents', $theMessage );

	  	echo $mailer->wrap_message( $email_heading, $theMessage );

		$message 		= ob_get_clean();
		$attachment = '';

		$mailer->send( $toEmail, $subject, $message, $headers, $attachment );

	}

    public function sendGiftcardEmail ( $giftCard ) {


		$expiry_date = wpr_get_giftcard_expiration( $giftCard );
		$date_format = get_option('date_format');
		ob_start();

		?>

		<div class="message">


			<?php _e( 'Dear', 'kodiak-giftcards' ); ?> <?php echo wpr_get_giftcard_to( $giftCard ); ?>,<br /><br />

			<?php $message = wpr_get_custom_message();
			if( $message == 'default' ) { ?>
				<?php echo wpr_get_giftcard_from( $giftCard ); ?> <?php _e('has selected a', 'kodiak-giftcards' ); ?> <strong><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></strong> <?php _e( 'Gift Card for you! This card can be used for online purchases at', 'kodiak-giftcards' ); ?> <?php bloginfo( 'name' ); ?>. <br />
			<?php } else {
				echo wpr_get_giftcard_from( $giftCard ); ?> <?php _e('has selected a', 'kodiak-giftcards' ); ?> <strong><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></strong> <?php _e( 'Gift Card for you!', 'kodiak-giftcards' ); ?>. <?php echo $message; ?> <br />
			<?php } ?>

			<h4><?php _e( 'Gift Card Amount', 'kodiak-giftcards' ); ?>: <?php echo wc_price( wpr_get_giftcard_balance( $giftCard ) ); ?></h4>
			<h4><?php _e( 'Gift Card Number', 'kodiak-giftcards' ); ?>: <?php echo get_the_title( $giftCard ); ?></h4>

			<?php
			if ( $expiry_date != "" ) {
				echo __( 'Expiration Date', 'kodiak-giftcards' ) . ': ' . date_i18n( get_option( 'date_format' ), strtotime( $expiry_date ) );
			}
			?>
		</div>

		<div style="padding-top: 10px; padding-bottom: 10px; border-top: 1px solid #ccc;">
			<?php echo wpr_get_giftcard_note( $giftCard ); ?>
		</div>

		<div style="padding-top: 10px; border-top: 1px solid #ccc;">

		<?php $instruction = wpr_get_custom_instructions();
		if( $instruction == 'default' ) { ?>
			<?php _e( 'Using your Gift Card is easy', 'kodiak-giftcards' ); ?>:

			<ol>
				<li><?php _e( 'Shop at', 'kodiak-giftcards' ); ?> <?php bloginfo( 'name' ); ?></li>
				<li><?php _e( 'Select "Pay with a Gift Card" during checkout.', 'kodiak-giftcards' ); ?></li>
				<li><?php _e( 'Enter your card number.', 'kodiak-giftcards' ); ?></li>
			</ol>
		</div>
		<?php } else {
			echo $instruction;
		}

		$return = ob_get_clean();
		return apply_filters( 'rpgc_email_content_return', $return, $giftCard );

	}
}


