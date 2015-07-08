<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-login" data-guest-login>
	<section class="es-login-box es-responsive wide">
		<div class="row">
			<div class="col-md-6 login-column">
				<div class="es-login-wrap">
					<form name="loginbox" id="loginbox" method="post" action="<?php echo JRoute::_( 'index.php' );?>" class="bs-docs-example">
						<div class="login-box-title"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_TO_ACCOUNT_TITLE' );?></div>
						<p class="text-center mb-20">
							<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW' );?>
						</p>
						<fieldset class="mt-20">
							<div class="form-group">
								<input type="text" class="form-control input-sm" name="username" placeholder="<?php echo $this->config->get( 'registrations.emailasusername' ) ? JText::_( 'COM_EASYSOCIAL_LOGIN_EMAIL_PLACEHOLDER', true ) : JText::_( 'COM_EASYSOCIAL_LOGIN_USERNAME_PLACEHOLDER' , true );?>" />
							</div>

							<div class="form-group">
								<input type="password" class="form-control input-sm" name="password" placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_PASSWORD_PLACEHOLDER' , true );?>" />
							</div>

							<label class="checkbox fd-small mt-10">
								<input type="checkbox" name="remember"> <span class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REMEMBER_YOU' );?></span>
							</label>

							<button type="submit" class="btn btn-es-success btn-block mt-20">
								<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_TO_ACCOUNT_BUTTON' );?>
							</button>
						</fieldset>

						<?php if( $this->config->get( 'oauth.facebook.registration.enabled' ) && $this->config->get( 'registrations.enabled' )
								&& $this->config->get( 'oauth.facebook.secret' )
								&& $this->config->get( 'oauth.facebook.app' )
							){ ?>
						<div class="center es-signin-social">
							<p class="line">
								<strong><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_SIGNIN_SOCIAL' );?></strong>
							</p>

							<?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ) ); ?>
						</div>
						<?php } ?>

						<hr />

						<div class="center">
							<a class="text-error" href="<?php echo FRoute::account( array( 'layout' => 'forgetUsername' ) );?>"> <?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_FORGOT_USERNAME' );?></a> /
							<a class="text-error" href="<?php echo FRoute::account( array( 'layout' => 'forgetPassword' ) );?>"> <?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_FORGOT_PASSWORD' );?></a>
						</div>

						<input type="hidden" name="option" value="com_easysocial" />
						<input type="hidden" name="controller" value="account" />
						<input type="hidden" name="task" value="login" />
						<input type="hidden" name="return" value="<?php echo $return; ?>" />
						<?php echo $this->html( 'form.token' );?>
					</form>
				</div>
			</div>

		<?php if( ( $this->config->get( 'registrations.enabled' ) && $this->config->get('general.site.lockdown.enabled') && $this->config->get('general.site.lockdown.registration') )
			|| ( $this->config->get( 'registrations.enabled' ) && !$this->config->get('general.site.lockdown.enabled') )
		){ ?>
		<div class="col-md-6 register-column">
			<form method="post" action="<?php echo JRoute::_( 'index.php' );?>" data-registermini-form>
				<div class="register-wrap <?php echo empty( $fields ) ? ' is-empty' : '';?>">
					<div class="login-box-title"><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_NO_ACCOUNT' );?></div>
					<p class="center mb-20">
						<?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW' );?>
					</p>

					<?php if( !empty( $fields ) ) { ?>
						<?php foreach( $fields as $field ) { ?>
							<div class="register-field" data-registermini-fields-item><?php echo $field->output; ?></div>
						<?php } ?>
					<?php } ?>

					<div class="clearfix">
						<button class="btn btn-es-primary btn-sm btn-register" type="button" data-registermini-submit><?php echo JText::_( 'COM_EASYSOCIAL_LOGIN_REGISTER_NOW_BUTTON' );?></button>
					</div>

					<?php if( $this->config->get( 'oauth.facebook.registration.enabled' ) && $this->config->get( 'registrations.enabled' )
							&& $this->config->get( 'oauth.facebook.secret' )
							&& $this->config->get( 'oauth.facebook.app' )
						){ ?>
						<div class="center es-signin-social">
							<p class="line">
								<strong><?php echo JText::_( 'COM_EASYSOCIAL_OR_REGISTER_WITH_YOUR_SOCIAL_IDENTITY' );?></strong>
							</p>

							<?php echo $facebook->getLoginButton( FRoute::registration( array( 'layout' => 'oauthDialog' , 'client' => 'facebook', 'external' => true ) , false ), false, 'popup', JText::_('COM_EASYSOCIAL_REGISTER_WITH_YOUR_FACEBOOK_ACCOUNT') ); ?>
						</div>
					<?php } ?>
				</div>

				<input type="hidden" name="option" value="com_easysocial" />
				<input type="hidden" name="controller" value="registration" />
				<input type="hidden" name="task" value="miniRegister" />
				<?php echo $this->html( 'form.token' );?>
			</form>
		</div>
		<?php } ?>
		</div>
	</section>
</div>
