<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package MantisBT
 * @copyright Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 * @copyright Copyright (C) 2002 - 2012  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link http://www.mantisbt.org
 *
 * @uses core.php
 * @uses access_api.php
 * @uses authentication_api.php
 * @uses config_api.php
 * @uses constant_inc.php
 * @uses form_api.php
 * @uses gpc_api.php
 * @uses html_api.php
 * @uses lang_api.php
 * @uses print_api.php
 * @uses user_api.php
 */

/**
 * MantisBT Core API's
 */
require_once( '../core.php' );
require_api( 'access_api.php' );
require_api( 'authentication_api.php' );
require_api( 'config_api.php' );
require_api( 'constant_inc.php' );
require_api( 'form_api.php' );
require_api( 'gpc_api.php' );
require_api( 'html_api.php' );
require_api( 'lang_api.php' );
require_api( 'print_api.php' );
require_api( 'user_api.php' );

form_security_validate('manage_user_reset');

auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_user_threshold' ) );

$f_user_id = gpc_get_int( 'user_id' );

user_ensure_exists( $f_user_id );

$t_user = user_get_row( $f_user_id );

# Ensure that the account to be reset is of equal or lower access to the
# current user.
access_ensure_global_level( $t_user['access_level'] );

$t_result = user_reset_password( $f_user_id );

form_security_purge('manage_user_reset');

if ( false == $t_result ) {
	# PROTECTED
	$t_msg = lang_get( 'account_reset_protected_msg' );
} else {
	# SUCCESS
	if ( ( ON == config_get( 'send_reset_password' ) ) && ( ON == config_get( 'enable_email_notification' ) ) ) {
		# send the new random password via email
		$t_msg = lang_get( 'account_reset_msg' );
	} else {
		# email notification disabled, then set the password to blank
		$t_msg = lang_get( 'account_reset_msg2' );
	}
}

print_successful_operation( 'manage_user_page.php', $t_msg );