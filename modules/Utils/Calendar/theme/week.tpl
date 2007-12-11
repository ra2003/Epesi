<div class="week-menu">
	<table border="0"><tr>
		<td style="padding-left: 180px;"></td>
		<td class="empty"></td>
		<td style="width: 10px;"></td>
		<td><a class="button" {$prev7_href}>{$prev7_label}&nbsp;&nbsp;<img border="0" width="8" height="8" src="{$theme_dir}/Utils_Calendar__prev.png"></a></td>
		<td><a class="button" {$prev_href}>{$prev_label}&nbsp;&nbsp;<img border="0" width="8" height="8" src="{$theme_dir}/Utils_Calendar__prev.png"></a></td>
		<td><a class="button" {$today_href}>{$today_label}&nbsp;&nbsp;<img border="0" width="8" height="8" src="{$theme_dir}/Utils_Calendar__this.png"></a></td>
		<td><a class="button" {$next_href}><img border="0" width="8" height="8" src="{$theme_dir}/Utils_Calendar__next.png">&nbsp;&nbsp;{$next_label}</a></td>
		<td><a class="button" {$next7_href}><img border="0" width="8" height="8" src="{$theme_dir}/Utils_Calendar__next.png">&nbsp;&nbsp;{$next7_label}</a></td>
		<td style="width: 10px;"></td>
		<td>{$popup_calendar}</td>
		<td class="empty"></td>
		<td class="add-info">{$info}</td>
	</tr></table>
</div>

<!-- SHADOW BEGIN -->
	<div class="layer" style="padding: 9px; width: 98%;">
		<div class="content_shadow">
<!-- -->

<div style="padding: 5px; background-color: #FFFFFF;">

	<table cellspacing=0 id="Utils_Calendar__week">
{* shows month *}
		<tr>
			<td class="hours_header" rowspan="2"><img src="{$theme_dir}/Utils_Calendar__icon-week.png" width="32" height="32" border="0"><br>{$week_view_label}</td>
			<td class=header_month colspan="{$header_month.first_span.colspan}">{$header_month.first_span.label}</td>
			{if isset($header_month.second_span)}
				<td class=header_month colspan="{$header_month.second_span.colspan}">{$header_month.second_span.label}</td>
			{/if}

		</tr>

{* this row contains days of month *}
		<tr>
			{foreach item=header from=$day_headers}
				<td class="header_day"><a href="">{$header}</a></td>
			{/foreach}
		</tr>

		<tr>
		{foreach key=k item=stamp from=$timeline}
			<tr>
				<td class="hour">{$stamp.label}</td>
				{foreach item=t key=j from=$time_ids}
				{if $j==$today}
				<td class="inter_today" id="{$t.$k}"></td>
				{else}
				<td class="inter" id="{$t.$k}"></td>
				{/if}
				{/foreach}
			</tr>
		{/foreach}

	</table>

</div>

<!-- SHADOW END -->
 		</div>
		<div class="shadow-top">
			<div class="left"></div>
			<div class="center"></div>
			<div class="right"></div>
		</div>
		<div class="shadow-middle">
			<div class="left"></div>
			<div class="right"></div>
		</div>
		<div class="shadow-bottom">
			<div class="left"></div>
			<div class="center"></div>
			<div class="right"></div>
		</div>
	</div>
<!-- -->
