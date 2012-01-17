{if isset($top_tags) AND is_array($top_tags)}
	<ul class="tags">
		{foreach from=$top_tags item=tag_freq}
			<li>{$tags->tagHTML($tag_freq.tag_id)}
				{if $tag_freq.frequency > 1}
					<span class="tag-frequency">x {$tag_freq.frequency}</span>
				{/if}
			</li>
		{/foreach}
	</ul>
{/if}
