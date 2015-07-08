<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="theme-helper-user btn-group btn-group-sm pull-right">
	<div class="btn-group btn-group-sm">
		<a href="#" class="btn btn-default btn-user dropdown-toggle" data-toggle="dropdown">
			<img class="avatar" src="<?php echo $my->getAvatar();?>" alt="<?php echo $modules->html( 'string.escape' , $my->getName() );?>" style="width:20px" />
			<span class="visible-lg visible-md">
				<?php echo JText::_( 'MOD_EASYSOCIAL_DROPDOWN_MENU_HI' );?>, <strong><?php echo $my->getName();?></strong>
				<i class="fa fa-angle-down"></i>
			</span>
		</a>
		<ul class="dropdown-menu">

			<?php if( $params->get( 'show_my_profile' , true ) ){ ?>
			<li>
				<a href="<?php echo $my->getPermalink();?>">
					<?php echo JText::_( 'MOD_EASYSOCIAL_DROPDOWN_MENU_MY_PROFILE' );?>
				</a>
			</li>
			<?php } ?>

			<?php if( $params->get( 'show_account_settings' , true ) ){ ?>
			<li>
				<a href="<?php echo FRoute::profile( array( 'layout' => 'edit' ) );?>">
					<?php echo JText::_( 'MOD_EASYSOCIAL_DROPDOWN_MENU_ACCOUNT_SETTINGS' );?>
				</a>
			</li>
			<?php } ?>

			<?php if( $items ){ ?>
				<?php foreach( $items as $item ){ ?>
				<li class="menu-<?php echo $item->id;?>">
					<a href="<?php echo $item->flink;?>"><?php echo $item->title;?></a>
				</li>
				<?php } ?>
			<?php } ?>	
		</ul>
	</div>

	<?php if( $params->get( 'show_sign_out' , true ) ){ ?>
	<a href="javascript:void(0);" onclick="document.getElementById('es-dropdown-logout-form').submit();" class="btn btn-default btn-logout visible-lg visible-md"><i class="fa fa-power-off"></i></a>
	<form class="logout-form" id="es-dropdown-logout-form">
		<input type="hidden" name="return" value="<?php echo $logoutReturn;?>" />
		<input type="hidden" name="option" value="com_easysocial" />
		<input type="hidden" name="controller" value="account" />
		<input type="hidden" name="task" value="logout" />
		<?php echo $modules->html( 'form.token' ); ?>
	</form>
	<?php } ?>

	<div class="dropdown btn-group btn-group-sm">
    	<a class="btn btn-default btn-logout" role="button" data-toggle="dropdown" href="#"><i class="fa fa-lock"></i></a>
        <ul class="dropdown-menu login-lock" role="menu" aria-labelledby="dLabel">
	        <form action="/" method="post" id="login-form" class="form-inline">
				<div style="margin-bottom:10px;padding:20px 20px 0;">
					<input type="text" autocomplete="off" size="18" class="form-control input-sm" name="username" id="es-username" tabindex="101">
				</div>

				<div style="margin-bottom:10px;padding:0 20px;">
					<input type="password" autocomplete="off" name="password" class="form-control input-sm" id="es-password" tabindex="102">
				</div>

				<div style="margin-bottom:10px;padding:0 20px;">
					<span class="checkbox mt-0">
						<input type="checkbox" value="yes" name="remember" id="remember" tabindex="103" style="margin-right:10px;">
						<label class="fd-small" for="remember">
							Remember me							</label>
					</span>
				</div>

				<div style="margin-bottom:10px;padding:0 20px;">
					<input type="submit" class="btn btn-primary" name="Submit" value="Login" tabindex="104" style="display:block;width:100%;">
				</div>

				<div class="item-social text-center" style="margin-bottom:10px;padding:0 20px;">
					<span data-oauth-facebook="">
						<div id="fb-root"></div>
						<a href="javascript:void(0);" class="btn btn-es-social btn-es-facebook" data-oauth-facebook-login="" data-oauth-facebook-appid="331262087036101" data-oauth-facebook-url="https://www.facebook.com/dialog/oauth?client_id=331262087036101&amp;redirect_uri=http%3A%2F%2Fdemo.stackideas.com%2Fregistration%2FoauthDialog%2Ffacebook&amp;state=0a1aed3c9ee1062bd023373d1d0317a7&amp;scope=publish_stream%2Cpublish_actions%2Cuser_relationships%2Cemail%2Cuser_birthday&amp;display=popup"><i class="ies-facebook"></i> Sign in with Facebook</a>
					</span>
				</div>


				<div class="dropdown-menu-footer">
					<ul class="unstyled">
						<li style="padding:10px 0;text-align:center;">
							<i class="ies-plus-2"></i>  <a href="/registration" class="pull-" tabindex="5">Create new account</a>
						</li>
						<li style="padding:10px 0;text-align:center;">
							<i class="ies-help"></i>  <a href="/account/lostusername" class="pull-" tabindex="6">I forgot my username</a>
						</li>
						<li style="padding:10px 0;text-align:center;">
							<i class="ies-help"></i>  <a href="/account/lostpassword" class="pull-" tabindex="6">I forgot my password</a>
						</li>
					</ul>
				</div>

				<input type="hidden" name="option" value="com_easysocial">
				<input type="hidden" name="controller" value="account">
				<input type="hidden" name="task" value="login">
				<input type="hidden" name="return" value="Lw==">
				<input type="hidden" name="3d843a140c5cb856e5633a005bd79fe3" value="1">
			</form>
        </ul>
	</div>

	<!-- <div class="dropdown btn-group btn-group-sm">
		<a class="btn btn-default btn-logout" role="button" data-toggle="dropdown" href="#"><i class="fa fa-lock"></i></a>
		<div class="dropdown-menu login-lock" role="menu" aria-labelledby="dLabel" style="padding:30px;">
			<form action="/wanderers/index.php" method="post" id="login-form" class="form-inline">
				<div class="panel-body">
					<div class="userdata">
						<div id="form-login-username" class="control-group">
							<div class="controls">
								<div class="input-prepend">
									<input type="text" autocomplete="off" size="18" class="form-control input-sm" name="username" id="es-username" tabindex="101">
								</div>
							</div>
						</div>

						<div id="form-login-password" class="control-group">
							<div class="controls">
								<div class="input-prepend">
									<input type="password" autocomplete="off" name="password" class="form-control input-sm" id="es-password" tabindex="102">
								</div>
							</div>
						</div>

						<div id="form-login-remember" class="control-group checkbox">
							<label for="modlgn-remember" class="control-label">Remember Me</label> <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes">
						</div>

						<div id="form-login-submit" class="control-group">
							<div class="controls">
								<button type="submit" tabindex="0" name="Submit" class="btn btn-primary">Log in</button>
							</div>
						</div>
					</div>

					<div class="user-account">
						<ul class="unstyled">
							<li>
								<a href="/wanderers/index.php/component/users/?view=registration">
									Create an account <span class="icon-arrow-right"></span></a>
							</li>
							
							<li>
								<a href="/wanderers/index.php/component/users/?view=remind">
									Forgot your username?</a>
							</li>

							<li>
								<a href="/wanderers/index.php/component/users/?view=reset">
								Forgot your password?</a>
							</li>
						</ul>
						<input type="hidden" name="option" value="com_users">
						<input type="hidden" name="task" value="user.login">
						<input type="hidden" name="return" value="aW5kZXgucGhwP0l0ZW1pZD0xMDE=">
						<input type="hidden" name="cc9514056635faf9b462fce8258894ad" value="1">
					</div>
				</div>
			</form>
		</div>
	</div> -->
</div>