<?php
	function active_login_modules()
	{
		global $config;
		
		// enable bzbb login and local login
		// the local login is only used to convert old users to bzbb users
		return array('bzbb' => (htmlspecialchars('http://my.bzflag.org/weblogin.php?action=weblogin&url=')
								. urlencode($config->value('baseaddress')
											. 'Login/' . '?bzbbauth=%TOKEN%,%USERNAME%')),
					 'local' => 1);
	}
?>