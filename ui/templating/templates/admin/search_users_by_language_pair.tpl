<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" >

<head>
    <!-- Editor Hint: ¿áéíóú -->
    <meta charset="utf-8" content="application/xhtml+xml" />
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/bootstrap/css/bootstrap.min1.css"/>
    <link rel="stylesheet" type="text/css" media="all" href="{urlFor name="home"}resources/css/style.1.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/jquery-ui.css"/>
    <link rel="stylesheet" href="{urlFor name="home"}resources/css/solas1.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="{urlFor name="home"}ui/js/lib/jquery-ui.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#myTable').DataTable(
          {
            "paging": false
          }
        );
      });
    </script>
</head>

<body>

{if isset($all_users) && count($all_users) > 0}

<table id="myTable" style="overflow-wrap: break-word; word-break:break-all;" class="container table table-striped">
  <thead>
    <th width="13%">Display Name</th>
    <th width="14%">Email</th>
    <th width="13%">Name</th>
    <th width="12%">Source Language</th>
    <th width="12%">Source Country</th>
    <th width="12%">Target Language</th>
    <th width="12%">Target Country</th>
    <th width="12%">Qualification Level</th>
  </thead>

  <tbody>
  {foreach $all_users as $user_row}

    <tr>
      <td><a href="{urlFor name="user-public-profile" options="user_id.{$user_row['user_id']}"}" target="_blank">{TemplateHelper::uiCleanseHTML($user_row['display_name'])}</a></td>
      <td>{$user_row['email']}</td>
      <td>{TemplateHelper::uiCleanseHTML($user_row['first_name'])} {TemplateHelper::uiCleanseHTML($user_row['last_name'])}</td>
      <td>{$user_row['language_name_source']}</td>
      <td>{$user_row['country_name_source']}</td>
      <td>{$user_row['language_name_target']}</td>
      <td>{$user_row['country_name_target']}</td>
      <td>{$user_row['qualification_level']}</td>
    </tr>

  {/foreach}
  </tbody>

</table>

{else}<p class="alert alert-info">No Users</p>{/if}

</body>
</html>
