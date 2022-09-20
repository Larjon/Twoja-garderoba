<?php
/**
 * Woo Extra Product Options - Field Editor
 *
 * @author    ThemeHiGH
 * @category  Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('THWVSF_Admin_Settings_Pro')):
class THWVSF_Admin_Settings_Pro extends THWVSF_Admin_Settings {
	
	protected static $_instance = null;

	public function __construct() {
		parent::__construct('pro');
		$this->page_id = 'pro';
	}

	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function render_page(){
		$this->render_tabs();
		$this->render_content();
	}

	private function render_content(){
		?>
		<div class="th-wrap-pro">
			<div class="th-nice-box">
			    <h2>Key Features of Variation Swatches Pro</h2>
			    <p>Variation Swatches Premium Version serves you more features for customizing your product variations and helps to make your product page more engaging.</p>
			    <ul class="feature-list star-list">
			        <li>
			        	<div class = "th-feat-head">5 Available Swatch Types</div>
			        	<div class = "th-feat-desc">The Premium version provides you with a total of 5 Swatch Types, 
									Label Swatches,Image Swatches,Color Swatches,Radio Swatches,Image with Label Swatches.
 						</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Swatches on Shop/Archive page</div>
			        	<div class = "th-feat-desc">Display variation swatches on WooCommerce Shop/Archive Page to view the availability of products</div>
			        </li>
			        <li>
			        	<div class = "th-feat-head">Swatch Display Styles</div>
			        	<div class = "th-feat-desc">Swatches can be displayed in five different styles, in addition to the default style: Swatch Dropdown, Slider, Accordion, Horizontal Scroller, and Vertical Scroller.</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Display Variation Price and Images on Shop Page</div>
			        	<div class = "th-feat-desc">Display each variant’s image and price with the variation swatches on the WooCommerce Shop page.</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Out of stock label</div>
			        	<div class = "th-feat-desc">Display an Out of Stock label to let your customers know about the existing variants without hiding the out of stock variations.</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Stock left alert</div>
			        	<div class = "th-feat-desc">Notify your customers about minimum stock remaining using the stock left alert feature.</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Separate Style Customization</div>
			        	<div class = "th-feat-desc">Swatches can be customized by styling the icon hover properties. </div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Swatches Alignment</div>
			        	<div class = "th-feat-desc">You can adjust the Swathes alignment on left,right or centre.</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Variation Limit to display</div>
			        	<div class = "th-feat-desc">Limit the number of variants to be displayed.</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Extra Tooltip Styling for Swatches  (Image, Term Name or Description)</div>
			        	<div class = "th-feat-desc">Pick a suitable color, text size, background color, etc from the Tooltip Styling tab to customize the tooltip..</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Add Multiple Swatch Design</div>
			        	<div class = "th-feat-desc">You can create as many designs for the swatches attribute for a single website.</div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Default dropdown to variation Image or Label swatches</div>
			        	<div class = "th-feat-desc">Default dropdown style can be auto converted to Label or Variation Image swatches. </div>
			        </li>
			         <li>
			        	<div class = "th-feat-head">Bicolor Swatches </div>
			        	<div class = "th-feat-desc">Show dual colours in a single swatch for products that have multiple colors.</div>
			        </li>

			    </ul>
			    <p>
			    	<a class="button big-button" target="_blank" href="https://www.themehigh.com/product/woocommerce-product-variation-swatches//?utm_source=free&utm_medium=premium_tab&utm_campaign=wpvs_upgrade_link">Upgrade to Premium Version</a>
			    	<a class="button big-button" target="_blank" href="https://flydemos.com/wpvs/?utm_source=free&utm_medium=banner&utm_campaign=wepo_trydemo" style="margin-left: 20px">Try Demo</a>
				</p>
			</div>
			<div class="th-flexbox">
			    
			    <div class="th-flexbox-child th-nice-box">
			        <h2>Additional Features </h2>
			        <p>Apart from the basic functionalities, the Premium version includes some additional features to make your store more appealing.</p>
			        <ul class="feature-list additional-feat">
			            <li>
			            	Display attributes as swatches:
			            	<p>Display attributes on filter widgets as swatches.</p>
			            </li>
			            <li>
			            	Share Product Variations as URL:
			            	<p>Create a product variation link to have a quick purchase or to share the selected variation.</p>
			            </li>
			            <li>
			            	Radio Swatch Styles:
			            	<p>Choose a suitable Radio Swatch style from the two given options.</p>
			            </li>
			            <li>
			            	Display swatches in additional info:
			            	<p>The variation swatches can be displayed on the Product page's Additional info section.</p>
			            </li>
			            <li>
			            	Search option Swatch design & attributes:
			            	<p>Save time by using the Search feature to find any Attributes or Swatch designs you want.</p>
			            </li>
			        </ul>
			    </div>
			</div>
			
		</div>
		<?php
	}

}
endif;