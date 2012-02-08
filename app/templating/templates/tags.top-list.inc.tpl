{if isset($top_tags) AND is_array($top_tags)}
	<ul class="tags">
		{foreach from=$top_tags item=tag}
			<li>
				{include file="inc.tag.tpl" tag=$tag}
			</li>
		{/foreach}
	</ul>
{/if}
