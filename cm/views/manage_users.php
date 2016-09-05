<?
if ($reset_password_for_user_id = array_get($_REQUEST, 'reset_password_for_user')) {
    do {
        if (!$obj = db_get_by_id('users', $reset_password_for_user_id)) {
            $error = 'Пользователь не найден!';
            break;
        }

        $new_password = generate_random_password();

        $hash = md5(trim($new_password));
        $obj['pass'] = $hash;
        db_update('users', $obj);

        $success = "Новый пароль для пользователя $obj[name]: '$new_password'";

    } while(0);
}

print_result();

?>



<table class="table table-hover">
    <thead>
    <tr>
        <th width="1%">#</th>
        <th>Имя</th>
        <th width="1%">Логин</th>
        <th width="1%">Действия</th>
    </tr>
    </thead>
    <tbody>
    <? foreach (users() as $list_user) {?>
        <tr>
            <td><?=$list_user['id']?></td>
            <td><?=$list_user['name']?></td>
            <td><?=$list_user['nick']?></td>
            <td>
                <a type="button" class="btn btn-default btn-xs" href="/cm/?view=manage_users&reset_password_for_user=<?=$list_user['id']?>">
                    <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span>
                    Сбросить пароль
                </a>
            </td>
        </tr>
    <?}?>
    </tbody>
</table>
