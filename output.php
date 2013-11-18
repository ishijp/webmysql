<?php
{
  require_once("config.php");
  require_once("MySmarty.class.php");
  session_name(SS_NAME);
  session_start();

  if (isset($_POST['Submit'])) {
    $submit = $_POST['Submit'];
  }
  else {
    $submit = "";
  }

  // 履歴消去処理
  if (isset($_GET['cm']) && $_GET['cm'] == "cl") {
    // セッション終了
    unset($_SESSION);
    session_destroy();
    $submit['Hist'] = "履歴消去";
  }


  if (isset($submit['Run']) && $_POST['USER_ID'] && $_POST['DBN'] && $_POST['QUERY']) {

    $user_id = trim($_POST['USER_ID']);
    $passwd = trim($_POST['PASSWD']);
    $dbn = trim($_POST['DBN']);
    $sid = trim($_POST['SID'])==""? "127.0.0.1": trim($_POST['SID']) ;
    $disp_space = isset($_POST['DISP_SPACE']);
    $single_command = isset($_POST['SINGLE_COMMAND']);
    $query_text = $_POST['QUERY'];
    if ($single_command) {
      $queries[0] = $query_text;
    }
    else {
      // 複数コマンド実行
      $queries = explode(";", $query_text);
    }
    $file_out = isset($_POST['FILE_OUT']);
    $has_output = False;
    $max_fetch_rows = $file_out? MAX_FOUT_FETCH_ROWS: MAX_FETCH_ROWS;

    $numcols = $numrows = 0;
    $colnames = array();
    $data = array();
    $affected_rows = -1;
    $emsg = "";

    $con = mysql_connect(trim($sid), $user_id, $passwd);

    mysql_select_db($dbn);

    if ($con) {
      foreach($queries as $query) {
	$query = trim($query);

	if (strlen($query) <= 0) {
	  continue;
	}

	//$stmt = mysql_query(mysql_escape_string($query), $con);
	$stmt = mysql_query($query, $con);

	$numrows = 0;
	$affected_rows = 0;

	if ($stmt) {
	  if (!($numrows = @mysql_num_rows($stmt))) {
	    $numrows = 0;
	  }

	  $row = array();
	  $colnames = array();
	  $data = array();

	  if ($numrows) {

	    for ($i=0; $i<$max_fetch_rows && $i<$numrows; $i++) {
	      if (!($row = mysql_fetch_array($stmt, MYSQL_ASSOC))) {
		break;
	      }
	      $data[] = $row;
	    }


	    $numcols = mysql_num_fields($stmt);

	    for ($j=0; $j<$numcols; $j++) {
	      $colnames[] = mysql_field_name($stmt, $j);
	    }

	    mysql_free_result($stmt);

	    $has_output = True;
	  }
	  else {
	    $affected_rows = mysql_affected_rows($con);
	  }
	}

	if ($errno = mysql_errno($con)) {
	  $emsg .= "MySQL " . mysql_errno().": " . mb_convert_encoding(mysql_error(), "SJIS") . "\n";
	}

      }
      mysql_close($con);

      $_SESSION['USER_ID'] = $user_id;
      $_SESSION['PASSWD'] = $passwd;
      $_SESSION['DBN'] = $dbn;
      $_SESSION['SID'] = $sid=="127.0.0.1"? "": $sid;
      $_SESSION['DISP_SPACE'] = $disp_space;
      $_SESSION['SINGLE_COMMAND'] = $single_command;

      // 「ファイル出力」は履歴に残さない
      //$_SESSION['FILE_OUT'] = $file_out;

      if (isset($_SESSION['QCOUNT'])) {
	$_SESSION['QCOUNT'] = ++$_SESSION['QCOUNT'] & HIST_SIZE;
	$_SESSION['Q_HIST'][$_SESSION['QCOUNT']] = $query_text;
      }
      else {
	$_SESSION['QCOUNT'] = 0;
	$_SESSION['Q_HIST'][0] = $query_text;
      }

    }
    else {
      $emsg = "MySQLの接続に失敗しました。";
    }

    $sm->assign("numrows", $numrows);
    $sm->assign("numcols", $numcols);
    $sm->assign("colnames", $colnames);
    $sm->assign("data", $data);
    $sm->assign("affected_rows", $affected_rows);
    $sm->assign("disp_space", $disp_space);
    $sm->assign("single_command", $single_command);
    //$sm->assign("file_out", $file_out);
    $sm->assign("emsg", $emsg);
    $sm->assign("mode", "Run");
    $sm->assign("has_output", $has_output);

    if ($has_output && $file_out) {
      $output_filename = F_NAME;

      header("Content-type: application/octet-stream");
      header("Content-Disposition: attachment; filename={$output_filename}");

      $delim = "";

      foreach ($colnames as $cname) {
	print($delim . $cname);
	$delim = F_DELIM;
      }

      print("\r\n");

      foreach ($data as $row) {
	$delim = "";
	foreach ($row as $val) {
	  print($delim . $val);
	  $delim = F_DELIM;
	}
	print("\r\n");
      }

      die();
    }
    else {
      $sm->display("myoutput.tpl");
    }
  }
  else if (isset($submit['Hist'])) {

    if (isset($_SESSION['QCOUNT'])) {
      $i = 0;
      $qc = $_SESSION['QCOUNT'];

      do {
	$q_hist[$i]['QUERY'] = $_SESSION['Q_HIST'][$qc];
	$q_hist[$i]['INDEX'] = $qc;
	$i++;
	$qc = --$qc & HIST_SIZE;

	if ($qc == $_SESSION['QCOUNT']) {
	  break;
	}

      } while (isset($_SESSION['Q_HIST'][$qc]));

      $sm->assign("q_hist", $q_hist);
    }
    $sm->assign("mode", "Hist");
    $sm->display("myoutput.tpl");
  }
}
?>
