<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo (isset($meta['title'])) ? $meta['title'] : 'Default Studio Name'; ?></title>
	<?php
		$css_files = Code::get(Code::CSS_FILES);
		foreach($css_files as $file)
		{
			echo html::style($file);
		}
		
		$js_files = Code::get(Code::JS_FILES);
		foreach($js_files as $file)
		{
			echo html::script($file);
		}
		
	?>

	<script type="text/javascript">
		$(function() {
			<?php echo Code::get(Code::JS_ONLOAD, FALSE); ?>
		});
		<?php echo Code::get(Code::JS, FALSE); ?>
	</script>
	
	<style type="text/css">
		<?php echo Code::get(Code::CSS, FALSE); ?>
	</style>
	
</head>

<body>
	
	<?php if(!isset($_panel_view) || $_panel_view == FALSE) : ?>
	
		<div id="container">
			
			<div id="header">
				<div id="menu-meta">
					<div class="last">
						<?php if(isset($admin) && $admin) echo $admin->name; ?>
						&bull;
						<a href="<?php echo url::site('admin/login/logout'); ?>">Logout</a>
					</div>
					<div class="first">
						
					</div>
					&nbsp;
				</div>
				<div id="menu-main">
					<h1 class="header-title">Brent Awesome</h1>
					<ul>
						<?php 
						$selected_page = FALSE;
						foreach($menu as $tab)
						{
							$selected = isset($tab['selected']) && $tab['selected'];
							echo '<li class="tab'.($selected ? ' selected' : '').'">';
							if($selected)
								echo '<strong>';
							else
								echo '<a href="'.url::site($tab['url']).'">';
								
							echo '<span>';
							
							echo $tab['name'];
							if(isset($tab['count']) && $tab['count'] !== FALSE)
							{
								echo '<span class="count">' . $tab['count'] . '</span>';
							}
							echo '</span>';
							if($selected)
								echo '</strong>';
							else
								echo '</a>';
							
							echo '</li>';
							
							if($selected)
							{
								$selected_page = $tab;
							}
							
							if($selected && !isset($page_children) && isset($tab['children']))
								$page_children = $tab['children'];
						} ?>
					</ul>
				</div>
			</div>
			<div id="page">
				<?php 
				$sub_menu = '';
				if(isset($page_children) && count($page_children) > 0 || isset($page_actions))
				{
					$sub_menu = '<ul class="menu-sub">';
					foreach($page_children as $child)
					{
						$selected = isset($child['selected']) && $child['selected'];
						$sub_menu .= '<li>';
						if($selected)
						{
							
							$sub_menu .= '<strong>';
							$sub_menu .= '<span><strong>'.$child['name'].'</strong></span>';
							$sub_menu .= '</strong>';
							if( isset($child['top']) && $child['top']==TRUE )
								$selected_page = $child;
						}
						else
						{
							$sub_menu .= '<a href="'.url::site($child['url']).'"><span>'.$child['name'].'</span></a>';
						}
						$sub_menu .= '</li>';
					}
					
					if(isset($page_actions))
						$sub_menu .= '<li class="actions">'.$page_actions.'</li>';
					
					$sub_menu .= '</ul>';
				}

				if(isset($breadcrumbs) && is_array($breadcrumbs))
				{
					if($selected_page)
						$page_title = '<a href="'.url::site($selected_page['url']).'">'.(isset($selected_page['title']) ? $selected_page['title'] : $selected_page['name']).'</a>';
					
					$page_title_sub = '';
					foreach($breadcrumbs as $bc_url=>$bc_text)
					{
						if($bc_url == $child['url']) continue;
						$page_title_sub .= ' / <span>';
						if(Request::instance()->uri() != $bc_url)
						{
							$page_title_sub .= '<a href="'.url::site($bc_url).'">'.$bc_text.'</a>';
						}
						else
						{
							$page_title_sub .= '<strong>'.$bc_text.'</strong>';
						}
						$page_title_sub .= '</span>';
					}
				}
				else
				{
					if(!isset($page_title))
						$page_title = isset($selected_page['title']) ? $selected_page['title'] : $selected_page['name'];
				}

				?>
				<h1 class="page-heading"><?php if(isset($page_title)) echo $page_title; if(isset($page_title_sub)) echo $page_title_sub; ?></h1>
				<?php echo $sub_menu . '<div class="clear"></div>'; ?>
				
				<?php if(isset($messages)) echo $messages; ?>
	
				<div id="content">
					<?php if(isset($body)) echo $body; ?>
					<div class="clear"></div>
				</div>
				<?php if(isset($notes)) 
				{
					echo '<script type="text/javascript">$(function() { $("#notes-show").click(function() { $("#notes-box").toggle(); $(this).find("span").toggle(); return false; }); }); </script>';
					echo '<div id="notes"><a href="#" id="notes-show"><span>Show Notes</span><span style="display:none;">Hide Notes</span></a><div id="notes-box">'.$notes.'</div></div>'; 
				}
				?>
			</div>
			<div id="footer">
				<div class="last">
					Powered by <a href="http://www.prismapp.com/" target="_blank">Prism</a>
				</div>
			</div>
		</div>
		
	<?php else : ?>
	
		<div id="header">
			<h1 class="header-title">Brent Awesome</h1>
		</div>
		<div id="page">
			<h1 class="page-heading"><?php if(isset($page_title)) echo $page_title; ?></h1>
			
			<?php if(isset($messages)) echo $messages; ?>
			
			<div id="content">
				<?php if(isset($body)) echo $body; ?>
				<div class="clear"></div>
			</div>

			<?php if(isset($notes)) 
			{
				echo '<script type="text/javascript">$(function() { $("#notes-show").click(function() { $("#notes-box").toggle(); $(this).find("span").toggle(); return false; }); }); </script>';
				echo '<div id="notes"><a href="#" id="notes-show"><span>Show Notes</span><span style="display:none;">Hide Notes</span></a><div id="notes-box">'.$notes.'</div></div>'; 
			}
			?>
		</div>
	
	
	<?php endif; ?>
	
</body>
</html>
