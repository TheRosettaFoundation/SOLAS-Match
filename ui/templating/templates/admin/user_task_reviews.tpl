<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas1.css"/>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="14%">Email</th>
    <th width="14%">Name</th>
    <th width="6%">Pair</th>
    <th width="6%">Accuracy</th>
    <th width="6%">Fluency</th>
    <th width="6%">Terminology</th>
    <th width="6%">Style</th>
    <th width="6%">Design</th>
    <th width="6%">Number</th>
    <th width="6%">Corrections</th>
    <th width="6%">Grammar</th>
    <th width="6%">Spelling</th>
    <th width="6%">consistency</th>
    <th width="6%">Number</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td>{$user_row['email']}</td>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</a></td>
      <td>{$user_row['language_pair']}</td>
      <td>{$user_row['accuracy']}</td>
      <td>{$user_row['fluency']}</td>
      <td>{$user_row['terminology']}</td>
      <td>{$user_row['style']}</td>
      <td>{$user_row['design']}</td>
      <td>{$user_row['num_new']}</td>
      <td>{$user_row['cor']}</td>
      <td>{$user_row['gram']}</td>
      <td>{$user_row['spell']}</td>
      <td>{$user_row['cons']}</td>
      <td>{$user_row['num_legacy']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
