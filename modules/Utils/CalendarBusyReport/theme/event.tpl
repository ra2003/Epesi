{php}
	load_js('modules/Utils/Calendar/theme/event_.js');
{/php}

<span class="event_menu" id="event_menu_{$event_id}" style="display:none;z-index:999;position:absolute;">
	{assign var=x value=$custom_actions|@count}
	{assign var=x value=$x*20}
	{assign var=x value=$x+100}
    <div class="layer" style="width: {$x}px;">

        <span class="event_menu_content" style="display: block;height: 20px;background-color: #e6ecf2;border:1px solid gray;">
			<span id="Utils_Calendar__event_images">
		    	{if isset($view_href)}
		            <a {$view_href}><img border="0" src="{$theme_dir}/Utils/Calendar/view.png"></a>
				{/if}
		    	{if isset($edit_href)}
		            <a {$edit_href}><img border="0" src="{$theme_dir}/Utils/Calendar/edit.png"></a>
				{/if}
		    	{if isset($delete_href)}
		            <a {$delete_href}><img border="0" src="{$theme_dir}/Utils/Calendar/delete.png"></a>
				{/if}
		    	{if isset($move_href)}
		            <a {$move_href}><img border="0" src="{$theme_dir}/Utils/Calendar/move.png"></a>
				{/if}
				{foreach from=$custom_actions item=action}
					<a {$action.href}><img border="0" src="{$action.icon}" /></a>
				{/foreach}
			</span>
        </span>

	</div>
</span>

{if $with_div}
<div id="utils_calendar_event:{$event_id}" class="utils_calendar_event" style="width:auto" onmouseover="this.oldZIndex=this.style.zIndex;this.style.zIndex=80;" onmouseout="this.style.zIndex=this.oldZIndex;">
{/if}
	<span id="Utils_Calendar__event" class="event_{$color}" style="width:auto" >
	    <div class="row">
{if !IPHONE}	    
	        <span id="event_info"><img {$tip_tag_attrs} src="{$theme_dir}/Utils/Calendar/info.png" onClick="event_menu('{$event_id}')" width="11" height="11" border="0"></span>
{else}
	        <span id="event_info"><img {$tip_tag_attrs} src="{$theme_dir}/Utils/Calendar/info_iphone.png" onClick="event_menu('{$event_id}')" width="26" height="26" border="0"></span>
{/if}
	        <div id="event_time">{if isset($view_href)}<a {$view_href}>{$start_time}</a>{else}{$start_time}{/if}</div>
	    </div>
	     <div class="row {if $draggable}{$handle_class}{/if}">
	        <span id="event_title">{$title_s}</span>
	    </div>
	</span>
{if $with_div}
</div>
{/if}