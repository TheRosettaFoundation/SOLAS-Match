{include file="header.inc.tpl"}
	<div class="grid_8">
		{if isset($tasks)}
			<h2 class="section_top">Translation Tasks</h2>
			{foreach from=$tasks item=task name=tasks_loop}
				<div class="task">
					<h3><a href="">{$task->title()}</a></h3>
					<p class="details">
						<span class="time_since">X days ago</span> <a href="">PeopleOrg</a>
					</p>
					<ul class="tags">
						
					</ul>
				</div>
			{/foreach}
		{/if}
	

		<div class="task">
			<h3><a href="#">Our Mission. Relieve poverty, support healthcar…</a></h3>
			<p class="details">
				<span class="time_since">1 day ago</span> <a href="#">PeopleOrg</a>
			</p>
			<ul class="tags">
				<li><a class="tag" href="tag-to-russian.html">To Russian</a></li>
				<li><a class="tag" href="tag-to-russian.html">From English</a></li>
				<li><a class="tag" href="tag-to-russian.html">review</a></li>
				<li><a class="tag" href="tag-to-russian.html">special olympics</a></li>
				<li><a class="tag" href="tag-to-russian.html">informal</a></li>
			</ul>
		</div>

<!--
		<div class="task">
			<h3><a href="#">Our Mission. Relieve poverty, support healthcar…</a></h3>
			<p class="details">
				<span class="time_since">1 day ago</span> <a href="#">PeopleOrg</a>
			</p>
			<ul class="tags">
				<li><a class="tag" href="tag-to-russian.html">To</a></li>
				<li><a class="tag" href="tag-to-russian.html">From</a></li>
				<li><a class="tag" href="tag-to-russian.html"></a></li>
			</ul>
		</div>
-->

		<div class="task">
			<h3><a href="#">We are a registered charity, and a large enviro…</a></h3>
			<p class="details">
				<span class="time_since">1 day ago</span> <a href="#">TransOrg</a>
			</p>
			<ul class="tags">
				<li><a class="tag" href="tag-to-russian.html">To Hindi</a></li>
				<li><a class="tag" href="tag-to-russian.html">From English</a></li>
				<li><a class="tag" href="tag-to-russian.html">translate</a></li>
				<li><a class="tag" href="tag-to-russian.html">inhouse</a></li>
			</ul>
		</div>
		
		<div class="task">
			<h3><a href="#">Qu'il s'agisse d'accueillir des populations…</a></h3>
			<p class="details">
				<span class="time_since">2 days ago</span> <a href="#">MedOrg</a>
			</p>
			<ul class="tags">
				<li><a class="tag" href="tag-to-russian.html">To English</a></li>
				<li><a class="tag" href="tag-to-russian.html">From French</a></li>
				<li><a class="tag" href="tag-to-russian.html">translate</a></li>
				<li><a class="tag" href="tag-to-russian.html">medical</a></li>
			</ul>
		</div>
		
		<div class="task">
			<h3><a href="#">Sign up to become a translator, project manag…</a></h3>
			<p class="details">
				<span class="time_since">3 day ago</span> <a href="#">PeopleOrg</a>
			</p>
			<ul class="tags">
				<li><a class="tag" href="tag-to-russian.html">To Russian</a></li>
				<li><a class="tag" href="tag-to-russian.html">From English</a></li>
				<li><a class="tag" href="tag-to-russian.html">special olympics</a></li>
				<li><a class="tag" href="tag-to-russian.html">translate</a></li>
				<li><a class="tag" href="tag-to-russian.html">informal</a></li>
			</ul>
		</div>
	</div>
	<div id="sidebar" class="grid_4">
		<ul class="tags">
			<li><a class="tag" href="tag-to-russian.html">To Russian</a> x 2</li>
			<li><a class="tag" href="tag-to-russian.html">To Hindi</a></li>
			<li><a class="tag" href="tag-to-russian.html">To English</a></li>
			<li><a class="tag" href="tag-to-russian.html">From English</a> x 3</li>
			<li><a class="tag" href="tag-to-russian.html">From French</a></li>
		</ul>
		<ul class="tags">
			<li><a class="tag" href="tag-to-russian.html">translate</a> x 3</li>
			<li><a class="tag" href="tag-to-russian.html">review</a></li>
		</ul>
		<ul class="tags">
			<li><a class="tag" href="tag-to-russian.html">special olympics</a> x 2</li>
			<li><a class="tag" href="tag-to-russian.html">informal</a> x 2</li>
			<li><a class="tag" href="tag-to-russian.html">medical</a></li>
			<li><a class="tag" href="tag-to-russian.html">inhouse</a></li>
		</ul>
	</div>
{include file="footer.inc.tpl"}
