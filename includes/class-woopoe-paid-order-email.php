<?php
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
/**
 * A custom Paid Order WooCommerce Email class
 *
 * @since 1.0
 * @extends \WC_Email
 */
class WC_Paid_Order_Email extends WC_Email {
	
/**
 * Emailm defaults
 *
 * @since 1.0
 */
public function __construct() {
 
    // set ID, this needs to be a unique name
    $this->id = 'wc_paid_order';
 
    // title in WooCommerce Email settings
    $this->title = 'Paid Order';
 
    // description in WooCommerce email settings
    $this->description = 'Paid Order Notification emails are sent when the order moves to Processing (paid) status after receiving PayPal IPN Confirmation';
 
    // default heading and subject lines. can be overridden using the settings
    $this->heading = 'Order {order_number} is paid';
    $this->subject = 'Order {order_number} is paid';
 
    // we are using default woocommerce templates
    $this->template_html  = 'emails/admin-new-order.php';
    $this->template_plain = 'emails/plain/admin-new-order.php';
 
    // trigger when others move from pending or failed to processing status
    add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
    add_action( 'woocommerce_order_status_failed_to_processing_notification',  array( $this, 'trigger' ) );
 
    // Call parent constructor to load any other defaults not defined
    parent::__construct();
 
    // this sets the recipient to the settings defined below in init_form_fields()
    $this->recipient = $this->get_option( 'recipient' );
 
    // if none was entered, just use the WP admin email as a fallback
    if ( ! $this->recipient )
        $this->recipient = get_option( 'admin_email' );
	}
	
	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 1.0
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {
	 
		// bail if no order ID is present
		if ( ! $order_id )
			return;
	 
		// setup order object
		$this->object = new WC_Order( $order_id );
	 
		// replace variables in the subject/headings
		$this->find[] = '{order_date}';
		$this->replace[] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );
	 
		$this->find[] = '{order_number}';
		$this->replace[] = $this->object->get_order_number();
	 
		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;
	 
		// send the email
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}
	
	/**
	 * get_content_html function.
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		woocommerce_get_template( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading()
		) );
		return ob_get_clean();
	}
	 
	 
	/**
	 * get_content_plain function.
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		woocommerce_get_template( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading()
		) );
		return ob_get_clean();
	}
	
	/**
	 * Initialize Settings Form Fields
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {
	 
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => 'Enable/Disable',
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'recipient'  => array(
				'title'       => 'Recipient(s)',
				'type'        => 'text',
				'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => 'Subject',
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => 'Email Heading',
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => 'Email type',
				'type'        => 'select',
				'description' => 'Choose which format of email to send.',
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => 'Plain text',
					'html'      => 'HTML', 'woocommerce',
					'multipart' => 'Multipart', 'woocommerce',
				)
			)
		);
	}
 
} // end \WC_Paid_Order_Email class