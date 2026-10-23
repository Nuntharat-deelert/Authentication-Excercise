<?php
require 'crud_bootstrap.php';
$pid=profile_id($conn); $rows=[];
$stmt=mysqli_prepare($conn,'SELECT id,skill_name,skill_level FROM skill WHERE profile_id = ? ORDER BY id DESC');
mysqli_stmt_bind_param($stmt,'i',$pid); mysqli_stmt_execute($stmt);mysqli_stmt_bind_result($stmt,$id,$name,$level);
while(mysqli_stmt_fetch($stmt))$rows[]=['id'=>$id,'skill_name'=>$name,'skill_level'=>$level];mysqli_stmt_close($stmt);

page_start('Manage Skills | CRUD Lab');

if(isset($_GET['success'])) echo '<p class="ok">Action completed successfully.</p>';
echo '<p class="muted">Existing resume skill records, all actions target the first demo profile.</p>';

echo '<table>
<thead>
    <tr>
        <th width="10%">ID</th>
        <th width="40%">Skill</th>
        <th width="25%">Level</th>
        <th width="25%" style="text-align:center;">Actions</th>
    </tr>
</thead>
<tbody>';

foreach($rows as $r){
    $lvlClass = strtolower($r['skill_level']);
    echo '<tr>
        <td>'.h($r['id']).'</td>
        <td><strong>'.h($r['skill_name']).'</strong></td>
        <td><span class="badge badge-'.$lvlClass.'">'.h($r['skill_level']).'</span></td>
        <td style="text-align:center;">
            <a href="skill_edit.php?id='.urlencode((string)$r['id']).'" class="btn-action edit-link">Edit</a>
            <a href="skill_delete.php?id='.urlencode((string)$r['id']).'" class="btn-action delete-link">Delete</a>
        </td>
    </tr>';
}
echo '</tbody></table>';

page_end();
