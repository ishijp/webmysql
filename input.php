<?php
{
  require_once("config.php");
  require_once("MySmarty.class.php");

  // HISTORYが指定された (HISTORY番号=qn)
  if (isset($_GET['qn'])) {
    session_name(SS_NAME);
    session_start();

    $sm->assign("uid", $_SESSION['USER_ID']);
    $sm->assign("pwd", $_SESSION['PASSWD']);
    $sm->assign("sid", $_SESSION['SID']);
    $sm->assign("dbn", $_SESSION['DBN']);
    $sm->assign("qry", $_SESSION['Q_HIST'][$_GET['qn']]);
    $sm->assign("spc", $_SESSION['DISP_SPACE']);
    $sm->assign("sgl", $_SESSION['SINGLE_COMMAND']);
    //$sm->assign("fot", $_SESSION['FILE_OUT']);
  }

  // クリア
  if (isset($_GET['cl'])) {
    session_name(SS_NAME);
    session_start();

    $sm->assign("uid", $_SESSION['USER_ID']);
    $sm->assign("pwd", $_SESSION['PASSWD']);
    $sm->assign("sid", $_SESSION['SID']);
    $sm->assign("dbn", $_SESSION['DBN']);

    $sm->assign("spc", $_SESSION['DISP_SPACE']);
    $sm->assign("sgl", $_SESSION['SINGLE_COMMAND']);
    //$sm->assign("fot", $_SESSION['FILE_OUT']);
  }

  $sm->assign("credit", "WebMySQL " . VERSION . "&nbsp;&nbsp;&copy;2009-2011 <i>iOSS</i>");
  $sm->display("myinput.tpl");

}
?>
