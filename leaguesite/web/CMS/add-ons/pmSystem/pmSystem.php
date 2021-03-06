<?php
	class pmSystem
	{
		function __construct($title)
		{
			global $site;
			global $config;
			global $user;
			global $tmpl;
			
			
			if (!isset($site))
			{
				require_once(dirname(dirname(dirname(__FILE__))) . '/site.php');
				$site = new site();
			}
			
			// no public private message folder
			if (user::getCurrentUserId() < 1)
			{
				$tmpl->setTemplate('NoPerm');
				$tmpl->assign('errorMsg', 'You need to login to access this content.');
				$tmpl->display();
				die();
			}
			
			// show messages in current mail folder
			// inbox is default
			$folder = 'inbox';
			if (isset($_GET['folder']) && strcmp($_GET['folder'], 'outbox') === 0)
			{
				$folder = 'outbox';
			}
			
			if (isset($_GET['add']))
			{
				require_once dirname(__FILE__) . '/pmAdd.php';
				new pmSystemAdd();
				die();
/*
			} elseif (isset($_GET['edit']))
			{
				require_once dirname(__FILE__) . '/pmEdit.php';
*/
			} elseif (isset($_GET['delete']))
			{
				require_once dirname(__FILE__) . '/pmDelete.php';
				new pmSystemDelete($folder, intval($_GET['delete']));
			} else
			{
				require_once dirname(__FILE__) . '/pmDisplay.php';
				$display = new pmSystemDisplay();
				
				switch(isset($_GET['view']))
				{
					case true: $display->showMail($folder, intval($_GET['view'])); break;
					default: $tmpl->assign('title', $title); $display->showMails($folder); break;
				}
			}
			
			$tmpl->display();
		}
	}
?>
