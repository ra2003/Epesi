<div>
	<a {$href}>
		<i class="fa fa-question-circle"></i> {$label}
	</a>
</div>
<div id="Base_Help__overlay" style="display:none;" onclick="Helper.hide_menu();"></div>
<img id="Base_Help__click_icon" frame1="{$theme_dir}/Base/Help/left_click.png" frame2="{$theme_dir}/Base/Help/left_click2.png" style="display:none;" />
<div id="Base_Help__menu" style="display:none;">
	<input type="text" id="Base_Help__search" placeholder="{$search_placeholder}" onkeyup="Helper.search_keypress()" />
	<div id="Base_Help__help_suggestions" class="tutorial_links">
	</div>
	<div id="Base_Help__help_links" class="tutorial_links" style="display:none;">
	</div>
	<div id="Base_Help__help_close_menu" class="tutorial_links" onclick="Helper.hide_menu();">
		{'Close'|t}<img src="{$theme_dir}/Base/Help/close_black.png" />
	</div>
</div>
