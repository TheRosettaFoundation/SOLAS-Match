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

{if isset($totals) && count($totals) > 0}

<table style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="25%">Language Pair</th>
    <th width="25%">Number of Translators</th>
    <th width="50%">Breakdown</th>
  </thead>

  <tbody>
  {foreach from=$totals key=pair item=total}

    <tr>
      <td>{$pair}</td>
      <td>{$total}</td>
      <td>{$breakdown[$pair]}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Data</p>{/if}

</body>
</html>
