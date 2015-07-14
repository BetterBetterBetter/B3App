<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

JHtml::_('behavior.modal');
//jimport( 'activity.socialintegration.profiledata' );

$comquick2cartHelper = new comquick2cartHelper;
$libclass = $comquick2cartHelper->getQtcSocialLibObj();

$mainframe = JFactory::getApplication();
$input = $mainframe->input;
$layout = $input->get('layout');
$params = JComponentHelper::getParams('com_quick2cart');

// Get store Owner.
if (!class_exists('storeHelper'))
{
	$path = JPATH_SITE . DS . 'components' . DS . 'com_quick2cart' . DS . 'helpers' . DS . 'storeHelper.php';
	JLoader::register('storeHelper', $path);
	JLoader::load('storeHelper');
}

$storeHelper = new storeHelper();
$storeOwner = $storeHelper->getStoreOwner($this->store_id);
$integrate_with = $params->get('integrate_with','none');

if ($integrate_with != 'none')
{
	$profile_url = $libclass->getProfileUrl(JFactory::getUser($storeOwner));
	$UserName = JFactory::getUser($storeOwner)->name;
	$profile_path = "<a alt='' href='".$profile_url."'>".$UserName."</a>";
}

if (!empty($this->storeDetailInfo))
{
	$sinfo = $this->storeDetailInfo;

	if ($layout == "storeinfo")
	{
		?>
		<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<?php
	}
	?>
			<div class="row-fluid">
				<div class="well well-small span12">
					<legend>
						<?php
						$storeHelper = new storeHelper();
						$storeLink   = $storeHelper->getStoreLink($this->storeDetailInfo['id']);
						?>

						<a href="<?php echo $storeLink; ?>" class="btn btn-mini">
							<i class="<?php echo Q2C_ICON_HOME;?>"></i>
						</a> &nbsp; <?php echo $sinfo['title']; ?>

						<?php
						if (empty($this->editstoreBtn))
						{
							$social_options= '';
							$dispatcher = JDispatcher::getInstance();
							JPluginHelper::importPlugin('system');
							$result = $dispatcher->trigger('onProductDisplaySocialOptions', array($this->storeDetailInfo['id'], 'com_quick2cart.vendor.storeinfo', $sinfo['title'], $storeLink));

							// Call the plugin and get the result
							if (!empty($result))
							{
								$social_options=$result[0];
							}

							if (!empty($social_options))
							{
								?>
									<span class="social_options">
										<?php echo $social_options; ?>
									</span>
								<?php
							}
						}

						if (!empty($this->editstoreBtn))
						{
							// JRoute::_('index.php?option=com_quick2cart&view=orders&layout=mycustomer'),'_self'
							if (!empty($this->store_id))
							{
								$storeid = $this->store_id;
								$createstore_Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');
								echo "<button type='button' title=".JText::_( 'SA_EDIT' )." class='btn  btn_margin pull-right btn-mini' onclick=\"window.open('".JRoute::_("index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=".$storeid."&Itemid=".$createstore_Itemid)."')\" >
									<i class='" . QTC_ICON_EDIT . "'></i></button>";
							}
						}

						if ($integrate_with != 'none')
						{
							?>
							<p style="font-size: 13px;"><?php echo JText::sprintf('COM_QUICK2CART_CREATED_BY',$profile_path); ?></p>
							<?php
						}
						?>
					</legend>

					<span>
						<?php
						$img='';

						if (!empty($sinfo['store_avatar']))
						{
							$img = $comquick2cartHelper->isValidImg($sinfo['store_avatar']);
						}

						if (empty($img))
						{
							$img = JUri::base().'components'.DS.'com_quick2cart'.DS.'assets' . DS . 'images'.DS.'default_store_image.png';
						}
						?>
						<img align="right" class='img-rounded img-polaroid qtc_putmargin5px' src="<?php echo $img;?>" alt="<?php echo  JText::_('QTC_IMG_NOT_FOUND') ?>"/>

						<p>
							<?php
							if ($layout=="storeinfo")
							{
								echo $sinfo['description'] ;
							}
							else
							{
								// GETTING STORE INFO LINK
								$vendor_Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor');
								$storeinfo_link=JRoute::_('index.php?option=com_quick2cart&view=vendor&layout=storeinfo&Itemid=0&store_id='.$sinfo['id'].'&tmpl=component');
								$description_length=strlen($sinfo['description'] );
								$params = JComponentHelper::getParams('com_quick2cart');
								$limit=$params->get("storeDescriptionLimit",100);
								$readmore = substr($sinfo['description'] , 0, $limit);

								if (!empty($readmore) && $limit < $description_length)
								{
									$readmore =$readmore." ...&nbsp;";
								}

								echo $readmore;

								// chk FOR CHAR LIMIT TO SHOW
								if ($limit < $description_length)
								{
									// SHOE READ MORE LINK
									?>
									<a title="<?php echo JText::_('QTC_READMORE')?>" class="modal" rel="{handler: 'iframe', size: {x: 720, y: 500}, onClose: function(){}}" class="modal " href="<?php echo $storeinfo_link;?>">
										<?php echo JText::_( 'QTC_READMORE' );?>
									</a>
									<?php
								}
							}
							?>
						</p>

						<!-- ADDRESS-->
						<?php if (!empty($sinfo['address'])){ ?>
							<address class="span4">
								<strong><?php echo JText::_('VENDER_ADDRESS'); ?></strong>
								<br/>
								<span><?php echo $sinfo['address']; ?></span>
							</address>
						<?php } ?>

						<address class="span4">
							<abbr title="Phone">
								<strong><?php echo JText::_('VENDER_CONTACT_INFO'); ?></strong>:
							</abbr>
							<?php echo $sinfo['phone']; ?>
							<br/> <?php echo $sinfo['store_email']; ?>
						</address>
					</span>
				</div>
			</div>
	<?php
	if ($layout=="storeinfo")
	{
		?>
		</div>
		<?php
	}
}
?>

