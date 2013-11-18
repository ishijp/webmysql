<?php
{
  define("VERSION", "1.15");
  define("LBDIR",preg_replace("/public_html\/.*$/", "Smarty", dirname(__FILE__)));
  define("SBDIR",preg_replace("/public_html\/.*$/", "smarty", dirname(__FILE__)));
  define("MAX_FETCH_ROWS", 2000); // 表示最大行数
  define("MAX_FOUT_FETCH_ROWS", 100000); // ファイル出力最大行数
  define("HIST_SIZE", 0x3f);	// 履歴サイズ (0x3f: 64個)
  define("SS_NAME", "webmysql");
  define("F_DELIM", "\t");	// ファイル出力の区切り文字
  define("F_NAME", "webmysql_out.txt");	// ファイル出力の出力ファイル名
}
?>
