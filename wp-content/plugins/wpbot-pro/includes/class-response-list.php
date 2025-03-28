<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Response_list extends WP_List_Table {


    public $chatbot_admin_page;
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Response', 'wpchatbot' ), //singular name of the listed records
			'plural'   => __( 'Responses', 'wpchatbot' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

        $this->chatbot_admin_page = admin_url('admin.php?page=simple-text-response');

	}


	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_responses( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}wpbot_response";

		$sql .= ' where 1';


		if(isset($_GET['strcat-filter']) && $_GET['strcat-filter']!=''){
			$sql .= " and `category` LIKE '%".$_GET['strcat-filter']."%'";
		}
		if(isset($_GET['strlang-filter']) && $_GET['strlang-filter']!=''){
			$sql .= " and `lang` = '".$_GET['strlang-filter']."'";
		}
		if(isset($_GET['str_search']) && $_GET['str_search']!=''){
			$sql .=" and (`query` like '%".$_GET['str_search']."%' or `keyword` LIKE '%".$_GET['str_search']."%' or `intent` LIKE '%".$_GET['str_search']."%')";
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

        

		return $result;
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_response( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}wpbot_response",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}wpbot_response";
		
		$sql .= ' where 1';

		if(isset($_GET['strcat-filter']) && $_GET['strcat-filter']!=''){
			$sql .= " and `category` LIKE '%".$_GET['strcat-filter']."%'";
		}
		if(isset($_GET['strlang-filter']) && $_GET['strlang-filter']!=''){
			$sql .= " and `lang` = '".$_GET['strlang-filter']."'";
		}
		if(isset($_GET['str_search']) && $_GET['str_search']!=''){
			$sql .=" and (`query` like '%".$_GET['str_search']."%' or `keyword` LIKE '%".$_GET['str_search']."%' or `intent` LIKE '%".$_GET['str_search']."%')";
		}

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No responses avaliable.', 'wpchatbot' );
	}

	
	function extra_tablenav( $which ) {
		
		global $wpdb;

		if(class_exists('Qcld_str_pro')){
			$table = $wpdb->prefix.'wpbot_response_category';
			$filterCat = (isset($_GET['strcat-filter'])?$_GET['strcat-filter']:'');
			$filterkey = (isset($_GET['str_search'])?$_GET['str_search']:'');
			if ( $which == "top" ){
				$categories = $wpdb->get_results("SELECT * FROM `$table` WHERE 1 ");
				?>
				<div class="alignleft actions bulkactions">

					<select name="strcat-filter" class="ewc-filter-cat">
						<option value="">Filter by Category</option>
						<?php 
						foreach($categories as $category){
							echo '<option value="'.$category->name.'" '.($filterCat==$category->name?'selected="selected"':'').' >'.$category->name.'</option>';
						}
						?>

					</select>
					<?php
					if( function_exists( 'qcld_wpbotml' ) ){
						$languages = qcld_wpbotml()->languages;
						$filterlang = (isset($_GET['strlang-filter'])?$_GET['strlang-filter']:'');
						?>
							<select name="strlang-filter" class="ewc-filter-cat">
								<option value="">Filter by Language</option>
								<?php 
								foreach($languages as $language){
									echo '<option value="'.$language.'" '.($filterlang==$language?'selected="selected"':'').' >'.qcld_wpbotml()->lanName( $language ).'</option>';
								}
								?>
							</select>
						<?php
					}
					?>
				</div>
				

				
				<div class="alignleft actions bulkactions">

					<input type="text" name="str_search" value="<?php echo $filterkey; ?>" placeholder="Enter keyword for search" />
					<?php submit_button( __( 'Filter' ), '', 'str_filter_action', false, array( 'id' => 'post-query-submit' ) ); ?>
					<?php submit_button( __( 'Clear Filter' ), '', 'str_clearfilter_action', false, array( 'id' => 'post-query-submit' ) ); ?>
				</div>
				
				<?php
			}
		}

		if ( $which == "bottom" ){
			//The code that goes after the table is there

		}
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {


		
		
		if(class_exists('Qcld_str_pro')){
			switch ( $column_name ) {
				case 'query':
				case 'keyword':
				case 'intent':
				case 'category':
					return $item[ $column_name ];
				
				case 'responses':
					return $item[ 'response' ];
				case 'lang':
					return $item[ 'lang' ];
				default:
					return print_r( $item, true ); //Show the whole array for troubleshooting purposes
			}
		}else{
			switch ( $column_name ) {
				case 'query':
				case 'keyword':
				case 'intent':
					return $item[ $column_name ];
				
				case 'responses':
					return $item[ 'response' ];
				case 'lang':
					return $item[ 'lang' ];
				default:
					return print_r( $item, true ); //Show the whole array for troubleshooting purposes
			}
		}
		
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_query( $item ) {

        $delete_nonce = wp_create_nonce( 'wp_delete_query' );
        $edit_nonce = wp_create_nonce( 'wp_edit_query' );

		$title = '<strong>' . $item['query'] . '</strong>';

		$actions = [
            'edit'  => sprintf( '<a href="?page=%s&action=%s&query=%s&_wpnonce=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $edit_nonce ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&query=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		
		
		if(class_exists('Qcld_str_pro')){
			$columns = [
				'cb'      => '<input type="checkbox" />',
				'query'    => __( 'Query', 'wpchatbot' ),
				'keyword'    => __( 'Keyword', 'wpchatbot' ),
				'responses' => __( 'Response', 'wpchatbot' ),
				'intent'=> __( 'Intent', 'wpchatbot' ),
				'category'=> __( 'Category', 'wpchatbot' ),
				'lang'=> __( 'Language', 'wpchatbot' ),
				
			];
		}else{
			$columns = [
				'cb'      => '<input type="checkbox" />',
				'query'    => __( 'Query', 'wpchatbot' ),
				'keyword'    => __( 'Keyword', 'wpchatbot' ),
				'responses' => __( 'Response', 'wpchatbot' ),
				'intent'=> __( 'Intent', 'wpchatbot' ),
				'lang'=> __( 'Language', 'wpchatbot' ),
				
			];
		}

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'query' => array( 'query', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'responses_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_responses( $per_page, $current_page );
	}

	public function process_bulk_action() {

        

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'wp_delete_query' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_response( absint( $_GET['query'] ) );

		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                //wp_redirect( esc_url_raw($this->chatbot_admin_page) );
				//exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );


			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_response( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw($this->chatbot_admin_page) );
			exit;
		}
	}

}