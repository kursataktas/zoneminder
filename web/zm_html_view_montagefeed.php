<?php
	if ( !canView( 'Stream' ) )
	{
		$view = "error";
		return;
	}
	if ( !isset($mode) )
	{
		if ( canStream() )
			$mode = "stream";
		else
			$mode = "still";
	}

	$result = mysql_query( "select * from Monitors where Id = '$mid'" );
	if ( !$result )
		die( mysql_error() );
	$monitor = mysql_fetch_assoc( $result );

	if ( $mode != "stream" )
	{
		// Prompt an image to be generated
		chdir( ZM_DIR_IMAGES );
		$status = exec( escapeshellcmd( ZMU_COMMAND." -m $mid -i" ) );
		chdir( '..' );
		if ( ZM_WEB_REFRESH_METHOD == "http" )
			header("Refresh: ".REFRESH_IMAGE."; URL=$PHP_SELF?view=montagefeed&mid=$mid&mode=still" );
	}
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");			  // HTTP/1.0
?>
<html>
<head>
<title>ZM - <?= $monitor['Name'] ?> - <?= $zmSlangFeed ?></title>
<link rel="stylesheet" href="zm_styles.css" type="text/css">
<script language="JavaScript">
<?php
	if ( $mode != "stream" && ZM_WEB_REFRESH_METHOD == "javascript" )
	{
?>
window.setTimeout( "window.location.reload(true)", <?= REFRESH_IMAGE*1000 ?> );
<?php
	}
?>
</script>
</head>
<body>
<table width="96%" align="center" border="0" cellspacing="0" cellpadding="4">
<tr>
<td width="50%" align="center" class="text"><b><?= $monitor['Name'] ?></b></td>
<?php if ( $mode == "stream" ) { ?>
<td width="50%" align="center" class="text"><a href="<?= $PHP_SELF ?>?view=montagefeed&mode=still&mid=<?= $mid ?>"><?= $zmSlangStills ?></a></td>
<?php } elseif ( canStream() ) { ?>
<td width="50%" align="center" class="text"><a href="<?= $PHP_SELF ?>?view=montagefeed&mode=stream&mid=<?= $mid ?>"><?= $zmSlangStream ?></a></td>
<?php } else { ?>
<td width="50%" align="center" class="text">&nbsp;</td>
<?php } ?>
</tr>
<?php
	if ( $mode == "stream" )
	{
		$stream_src = ZM_PATH_ZMS."?monitor=".$monitor['Id']."&idle=".STREAM_IDLE_DELAY."&refresh=".STREAM_FRAME_DELAY;
		if ( isNetscape() )
		{
?>
<tr><td colspan="2" align="center"><img src="<?= $stream_src ?>" border="0" width="<?= $monitor['Width'] ?>" height="<?= $monitor['Height'] ?>"></td></tr>
<?php
		}
		else
		{
?>
<tr><td colspan="2" align="center"><applet code="com.charliemouse.cambozola.Viewer" archive="<?= ZM_PATH_CAMBOZOLA ?>" align="middle" width="<?= $monitor['Width'] ?>" height="<?= $monitor['Height'] ?>"><param name="url" value="<?= $stream_src ?>"></applet></td></tr>
<?php
		}
	}
	else
	{
?>
<tr><td colspan="2" align="center"><img src="<?= ZM_DIR_IMAGES.'/'.$monitor['Name'] ?>.jpg" border="0" width="<?= $monitor['Width'] ?>" height="<?= $monitor['Height'] ?>"></td></tr>
<?php
	}
?>
</table>
</body>
</html>
