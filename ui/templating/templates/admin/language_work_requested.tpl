<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas1.css"/>
</head>

<body>

{if isset($words) && count($words) > 0}

<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th>Language Pair</th>
    {foreach $years as $year}
    <th>{$year} Tasks</th>
    <th>{$year} Words</th>
    {/foreach}
  </thead>

  <tbody>
  {foreach from=$words key=key item=row}

    <tr>
      <td>$key</td>
      {foreach $years as $year}
      <td>{if !empty($row[$year]['tasks'])}{$row[$year]['tasks']}{/if}</td>
      <td>{if !empty($row[$year]['words'])}{$row[$year]['words']}{/if}</td>
      {/foreach}
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Data</p>{/if}

</body>
</html>
