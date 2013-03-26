{include file="header.tpl"}
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Sample 1</a></li>
        <li><a href="#tabs-2">Graph</a></li>
        <li><a href="#tabs-3">Sample 2</a></li>
    </ul>
    <div id="tabs-1">
        <p>her is a sample tab</p>
    </div>
    <div id="tabs-2">
        {$body}
    </div>
    <div id="tabs-3">
        <p>And a final tab</p>
    </div>
</div
{include file="footer.tpl"}

