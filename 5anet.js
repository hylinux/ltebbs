function header_quick_form_login() {
    var username = $F('quick_user_name');
    var password = $F('quick_user_password');

    if ( !username ) {
        alert("登录用户名不能为空");
        return false;
    }

    if ( !password ) {
        alert("用户密码不能为空");
        return false;
    }

    var quick_login_form = $('quick_login_form');

    quick_login_form.submit();
}

function changeTheme() {
    var choiceItem = $F('header_changetheme');

    var url = 'index.php?module=user&action=changetheme&id=';

    if ( choiceItem == 'default' || 
         choiceItem == 'new' ||
        choiceItem == 'newll' )  {
        document.location.href=url + choiceItem;
    } else {
        return false;
    }
}


//检查注册时用户名是否为空
function checkRegisterUserName() {
    var check_username = $F('register_username');
    var check_username_o = $('register_username');

    var msg_div = $('showusername_div');


    if ( !check_username ) {
        msg_div.innerHTML = '<font color=red>用户名不能为空</font>';
        check_username_o.focus();
        return false;
    }

    if ( check_username.length > 40 ) {
        msg_div.innerHTML = '<font color=red>用户名长度不能超过40个字符或者是20个汉子</font>';
        check_username_o.focus();
        return false;
    }
    msg_div.innerHTML = '';

}


//检查用户名是否被注册
function checkRegisterUserName() {
    var check_username = $F('register_username');
    var check_username_o = $('register_username');
    var msg_div = $('showusername_div');

    if ( !check_username ) {
        msg_div.innerHTML = '<font color=red>用户名不能为空</font>';
        check_username_o.focus();
        return false;
    }

    if ( check_username.length > 40 ) {
        msg_div.innerHTML = '<font color=red>用户名长度不能超过40个字符或者是20个汉子</font>';
        check_username_o.focus();
        return false;
    }

    var url = 'index.php?module=user&action=checkuser&username=' + check_username;

    var myajax = new Ajax.Updater(
        {
            success:'showusername_div'},
        url,
        {
            method:'get'
        }
    );
}


//检查email格式是否正确，并检查email
//地址是否被使用了
function checkRegisterUserEmail() {
   var user_email = $F('register_useremail');
   var user_email_o = $('register_useremail');
   var showuseremail_div = $('showuseremail_div');

    if ( !user_email ) {
        showuseremail_div.innerHTML = '<font color=red>用户邮件不能为空</font>';
        user_email_o.focus();
        return false;
    }

    if ( user_email.length > 85 ) {
        showuseremail_div.innerHTML = '<font color=red>用户邮件长度不能超过85个字符</font>';
        user_email_o.focus();
        return false;
    }

    var re = /^[\w|\.|-]{1,16}@[a-zA-Z0-9-]{1,15}(?:\.[a-zA-Z]{2,5}){1,2}$/;

    if ( !re.test(user_email) ) {
        showuseremail_div.innerHTML = '<font color=red>用户邮件格式不正确</font>';
        user_email_o.focus();
        return false;
    }


    var url = 'index.php?module=user&action=checkemail&useremail=' + user_email;

    var myajax = new Ajax.Updater(
        {
            success:'showuseremail_div'},
        url,
        {
            method:'get'
        }
    );
}




function checkpassword() {
    var p1 = $F('password');
    var mydiv = $('showmsg_p1');

    if ( p1 == null || p1.length <= 0 ) {
        mydiv.style.display = '';
        mydiv.innerHTML = "<font color='red'>密码不能为空</font>";
        return;
    }

    if ( p1.length < 6 ) {
        mydiv.style.display = '';
        mydiv.innerHTML = "<font color='red'>密码长度不能小于6位</font>";
        return;
    }

    mydiv.style.display = 'none';
}


function checkp2() {
    var p1 = $F('password');
    var p2 = $F('re-password');
    var mydiv = $('showmsg_p2');

    if ( p2 == null || p2.length <= 0 ) {
        mydiv.style.display = '';
        mydiv.innerHTML = "<font color='red'>密码不能为空</font>";
        return;
    }

    if ( p2.length < 6 ) {
        mydiv.style.display = '';
        mydiv.innerHTML = "<font color='red'>密码长度不能小于6位</font>";
        return;
    }


    if ( p1 != p2 ) {
        mydiv.style.display = '';
        mydiv.innerHTML = "<font color='red'>两次输入密码不一致</font>";
        return;
    }

    mydiv.style.display = 'none';

    }


//加查验证码
function checkRegisterCode() {
    var checkcode = $F('register_checkcode');
    var checkcode_o = $('register_checkcode');

    var showdiv = $('showcheckcode_div');

    if ( !checkcode ) {
        showdiv.innerHTML = '<font color=red>验证码为空,请输入上面图片中的验证码</font>';
        checkcode_o.focus();
        return false;
    }

    var url = 'index.php?module=user&action=checkcode&code=' + checkcode;

    var myajax = new Ajax.Updater(
        {
            success:'showcheckcode_div'},
        url,
        {
            method:'get'
        }
    );

}

//验证老密码
function checkoldpassword() {
    var oldpassword = $F('oldpassword');
    var oldpassword_o = $('oldpassword');
    var show_po = $('showmsg_po');

    if ( !oldpassword ) {
        show_po.innerHTML = '<font color=red>原始密码为空，请输入原始密码验证身份</font>';
        oldpassword_o.focus();
        return false;
    }

    var url = 'index.php?module=user&action=checkpassword&password=' + oldpassword;

    var myajax = new Ajax.Updater(
        {
            success:'showmsg_po'},
        url,
        {
            method:'get'
        }
    );

}




